@if ($paginator->hasPages())
    <nav class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm" role="navigation" aria-label="Pagination">
        
        {{-- Informations sur la page --}}
        <div class="text-slate-500 order-2 sm:order-1">
            @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
                Affichage de 
                <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span> 
                à 
                <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span> 
                sur 
                <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> 
                résultat(s)
            @else
                Page <span class="font-semibold text-slate-800">{{ $paginator->currentPage() }}</span>
            @endif
        </div>

        {{-- Boutons de navigation --}}
        <div class="flex items-center gap-2 order-1 sm:order-2">
            
            {{-- Bouton Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-400 cursor-not-allowed select-none" aria-disabled="true" title="Page précédente">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Précédent</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" 
                   class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-700 font-medium hover:bg-slate-50 hover:border-brand-blue hover:text-brand-blue transition-all shadow-sm" title="Page précédente">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Précédent</span>
                </a>
            @endif

            {{-- Bouton Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" 
                   class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-700 font-medium hover:bg-slate-50 hover:border-brand-blue hover:text-brand-blue transition-all shadow-sm" title="Page suivante">
                    <span class="hidden sm:inline">Suivant</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-slate-400 cursor-not-allowed select-none" aria-disabled="true" title="Page suivante">
                    <span class="hidden sm:inline">Suivant</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif

        </div>
    </nav>
@endif