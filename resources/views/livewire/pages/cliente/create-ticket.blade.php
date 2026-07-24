<div class="p-8">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Olá, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-zinc-400 mt-1">Bem-vindo à sua Central de Atendimento. Acompanhe ou inicie novos suportes
                abaixo.</p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="bg-zinc-50 dark:bg-slate-900  border border-zinc-800 rounded-xl p-8">
        <h2 class="text-white font-semibold text-lg">Criar Nova Solicitação</h2>
        <p class="text-sm text-zinc-500 mt-1 mb-6">Preencha os detalhes técnicos para que nossa equipe especializada
            possa te ajudar.</p>

        <div class="border-t border-zinc-800 mb-6"></div>

        <form wire:submit="createTicket" class="space-y-6">
            <flux:input
                label="Assunto / Resumo do Problema"
                placeholder="Ex: Não consigo finalizar pagamentos na rota de checkout ou erro 500"
                wire:model="form.titulo"
            />

            <flux:textarea
                label="Descrição Detalhada do Ocorrido"
                rows="4"
                placeholder="Por favor, forneça o máximo de detalhes possível, como prints, passos para reproduzir, mensagens de erro do console ou logs da sua aplicação."
                wire:model="form.descricao"
            />

            <div>
                <flux:label>Categoria Relacionada</flux:label>
                <div class="grid grid-cols-4 gap-3 mt-1.5">
                    @foreach ($this->categories as $value => $cat)
                        <button
                            type="button"
                            wire:click="$set('form.categoria', '{{ $value }}')"
                            class="flex flex-col items-center gap-2 py-4 rounded-lg border transition-colors {{ $form->categoria === $value ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600' }}"
                        >
                            <flux:icon name="{{ $cat['icon'] }}" class="w-5 h-5"/>
                            <span class="text-sm font-medium">{{ $cat['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <flux:label>Grau de Urgência / Prioridade</flux:label>
                <div class="grid grid-cols-4 gap-3 mt-1.5">
                    @foreach ($this->priorityOptions as $value => $label)
                        <button
                            type="button"
                            wire:click="$set('form.prioridade', '{{ $value }}')"
                            class="py-2.5 rounded-lg border text-sm font-medium transition-colors {{ $form->prioridade === $value ? 'border-indigo-500 text-indigo-400 bg-indigo-500/5' : 'border-zinc-700 text-zinc-400 hover:border-zinc-600' }}"
                        >
                            {{ $label['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">
                <flux:button type="button" variant="ghost" wire:click="cancel">Cancelar</flux:button>
                <flux:button type="submit" variant="primary">Registrar Chamado</flux:button>
            </div>
        </form>
    </div>
</div>
