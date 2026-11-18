<?php

namespace App\Http\Controllers;

use App\Models\Bureau;
use App\Models\Etage;
use App\Models\User;
use App\Support\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BureauController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $archives = $user->isAdmin() && $request->boolean('archives');

        $bureaux = Bureau::query()
            ->when($archives, fn ($q) => $q->onlyTrashed())
            ->with('etage')->withCount(['biens', 'incidents as incidents_ouverts_count' => fn ($q) => $q->ouverts()])
            ->when($request->etage_id, fn ($q, $v) => $q->where('etage_id', $v))
            ->when($request->service, fn ($q, $v) => $q->where('service', 'like', "%$v%"))
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('numero', 'like', "%$v%")->orWhere('libelle', 'like', "%$v%")))
            ->orderBy('numero')->paginate(20)->withQueryString();

        return view('bureaux.index', [
            'bureaux' => $bureaux,
            'etages' => Etage::orderBy('ordre')->get(),
            'archives' => $archives,
        ]);
    }

    /** Point d'entrée du QR code : /b/{numero} → fiche du bureau (après connexion). */
    public function scan(string $numero)
    {
        $bureau = Bureau::where('numero', $numero)->firstOrFail();

        return redirect()->route('bureaux.show', $bureau);
    }

    public function create()
    {
        return view('bureaux.form', [
            'bureau' => new Bureau,
            'etages' => Etage::orderBy('ordre')->get(),
            'agents' => User::where('active', true)->orderBy('name')->get(),
            'affectes' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9\-_\.]+$/', Rule::unique('bureaux', 'numero')],
            'libelle' => ['required', 'string', 'max:150'],
            'etage_id' => ['required', 'exists:etages,id'],
            'service' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'agents' => ['nullable', 'array'],
            'agents.*' => ['exists:users,id'],
        ]);
        $data['numero'] = strtoupper($data['numero']);

        $bureau = Bureau::create($data);
        $bureau->agents()->sync($data['agents'] ?? []);
        Activity::log('bureau_cree', $bureau, "Création du bureau {$bureau->numero}");

        return redirect()->route('bureaux.show', $bureau)->with('success', 'Bureau créé. Son QR code est disponible ci-dessous.');
    }

    public function show(Request $request, Bureau $bureau)
    {
        $bureau->load(['etage', 'agents']);
        $biens = $bureau->biens()->with('categorie')->orderBy('designation')->get();
        $incidents = $bureau->incidents()->with(['type', 'declarant'])->latest()->limit(20)->get();
        $qrSvg = QrCode::format('svg')->size(180)->margin(1)->errorCorrection('M')->generate($bureau->scanUrl());

        return view('bureaux.show', compact('bureau', 'biens', 'incidents', 'qrSvg'));
    }

    public function edit(Bureau $bureau)
    {
        return view('bureaux.form', [
            'bureau' => $bureau,
            'etages' => Etage::orderBy('ordre')->get(),
            'agents' => User::where('active', true)->orderBy('name')->get(),
            'affectes' => $bureau->agents()->pluck('users.id')->all(),
        ]);
    }

    public function update(Request $request, Bureau $bureau)
    {
        $data = $request->validate([
            'libelle' => ['required', 'string', 'max:150'],
            'etage_id' => ['required', 'exists:etages,id'],
            'service' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'agents' => ['nullable', 'array'],
            'agents.*' => ['exists:users,id'],
        ]);

        $bureau->update(collect($data)->except('agents')->all()); // numéro non modifiable
        $bureau->agents()->sync($data['agents'] ?? []);
        Activity::log('bureau_modifie', $bureau, "Modification du bureau {$bureau->numero}");

        return redirect()->route('bureaux.show', $bureau)->with('success', 'Bureau mis à jour.');
    }

    /** Archivage (suppression logique, l'historique est conservé). */
    public function destroy(Bureau $bureau)
    {
        if ($bureau->biens()->exists()) {
            return back()->with('error', 'Ce bureau contient encore des biens : transférez-les avant de l\'archiver.');
        }

        $bureau->delete();
        Activity::log('bureau_archive', $bureau, "Archivage du bureau {$bureau->numero}");

        return redirect()->route('bureaux.index')->with('success', 'Bureau archivé.');
    }

    public function restore(Bureau $bureau)
    {
        $bureau->restore();
        Activity::log('bureau_restaure', $bureau, "Restauration du bureau {$bureau->numero}");

        return redirect()->route('bureaux.show', $bureau)->with('success', 'Bureau restauré.');
    }

    /** Téléchargement du QR code : ?format=svg (défaut) ou png (nécessite l'extension imagick). */
    public function qr(Request $request, Bureau $bureau)
    {
        $nom = 'QR-'.$bureau->numero;

        if ($request->query('format') === 'png' && extension_loaded('imagick')) {
            $png = QrCode::format('png')->size(600)->margin(2)->errorCorrection('M')->generate($bureau->scanUrl());

            return response($png, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="'.$nom.'.png"',
            ]);
        }

        $svg = QrCode::format('svg')->size(600)->margin(2)->errorCorrection('M')->generate($bureau->scanUrl());

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$nom.'.svg"',
        ]);
    }

    /** Étiquette PDF d'un bureau. */
    public function qrPdf(Bureau $bureau)
    {
        return Pdf::loadView('bureaux.qr_pdf', ['bureaux' => collect([$bureau->load('etage')]), 'qr' => $this->qrBase64(...)])
            ->setPaper('a5')->download('QR-'.$bureau->numero.'.pdf');
    }

    /** Planche de QR codes de tous les bureaux (ou d'un étage) pour impression. */
    public function planche(Request $request)
    {
        $bureaux = Bureau::with('etage')
            ->when($request->etage_id, fn ($q, $v) => $q->where('etage_id', $v))
            ->orderBy('numero')->get();

        return Pdf::loadView('bureaux.qr_pdf', ['bureaux' => $bureaux, 'qr' => $this->qrBase64(...)])
            ->setPaper('a4')->download('QR-bureaux.pdf');
    }

    private function qrBase64(Bureau $bureau): string
    {
        $svg = QrCode::format('svg')->size(300)->margin(1)->errorCorrection('M')->generate($bureau->scanUrl());

        return 'data:image/svg+xml;base64,'.base64_encode((string) $svg);
    }
}
