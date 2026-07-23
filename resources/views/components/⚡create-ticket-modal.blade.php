<?php


use App\Livewire\Forms\CreateTicketForm;
use App\Services\TicketService;
use Livewire\Attributes\On;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {

    use WithFileUploads;


    public bool $showModal = false;


    public CreateTicketForm $form;

    public function mount(): void
    {
        $this->form = new CreateTicketForm($this, 'form');
    }

    protected array $priorityOptions = [
        'Baixa' => [
            'label' => 'Baixa',
            'active' => 'border-zinc-500 text-zinc-300 bg-zinc-800',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Media' => [
            'label' => 'Média',
            'active' => 'border-indigo-500 text-indigo-400 bg-indigo-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Alta' => [
            'label' => 'Alta',
            'active' => 'border-amber-500 text-amber-400 bg-amber-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],

        'Urgente' => [
            'label' => 'Urgente',
            'active' => 'border-red-500 text-red-400 bg-red-500/10',
            'inactive' => 'border-zinc-700 text-zinc-400'
        ],
    ];


    public function open(): void
    {
        $this->form->resetForm();

        $this->resetValidation();

        $this->showModal = true;
    }


    public function close(): void
    {
        Flux::modal('create-ticket-modal')->close();

        $this->resetValidation();
    }


    public function removeAttachment(int $index): void
    {
        unset($this->form->anexos[$index]);

        $this->form->anexos = array_values(
            $this->form->anexos
        );
    }


    public function createTicket(TicketService $service): void
    {
        $data = $this->form->validate();


         $service->createTicket($data);


        $this->form->resetForm();


        Flux::modal('create-ticket-modal')->close();


        $this->dispatch(
            'ticket-created',
            message: 'Chamado criado com sucesso!');

    }

    #[On('ticket-created')]
    public function showSuccess($message): void
    {
        Flux::toast(
            text: $message,
            variant: 'success',
            position: "top end"
        );
    }

};

?>

<div>

    <form wire:submit="createTicket" class="space-y-6">


        {{-- Mensagem sucesso --}}
        @if(session()->has('success'))

            <div class="rounded-lg bg-green-500/10 border border-green-500/30 p-3 text-green-400">
                {{ session('success') }}
            </div>

        @endif



        {{-- Erros gerais --}}
        @if($errors->any())

            <div class="rounded-lg bg-red-500/10 border border-red-500/30 p-4">

                <p class="text-red-400 font-medium">
                    Corrija os seguintes erros:
                </p>


                <ul class="mt-2 list-disc list-inside text-sm text-red-300">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        <div>

            <flux:heading size="lg">
                Novo Chamado de Suporte
            </flux:heading>


            <flux:subheading>
                Preencha os detalhes para registrar a solicitação.
            </flux:subheading>

        </div>


        {{-- Título --}}
        <div>

            <flux:input
                label="Assunto do Chamado"
                placeholder="Ex: Erro ao emitir boleto"
                wire:model="form.titulo"
            />


            @error('form.titulo')

            <p class="text-red-400 text-sm mt-1">
                {{ $message }}
            </p>

            @enderror

        </div>


        {{-- Categoria + Prioridade --}}
        <div class="grid grid-cols-2 gap-4">


            <div>

                <flux:select
                    label="Departamento / Categoria"
                    wire:model="form.categoria"
                >

                    <flux:select.option value="TI">
                        Suporte Técnico
                    </flux:select.option>


                    <flux:select.option value="financeiro">
                        Financeiro
                    </flux:select.option>


                    <flux:select.option value="RH">
                        Recursos Humanos
                    </flux:select.option>


                    <flux:select.option value="Geral">
                        Atendimento Geral
                    </flux:select.option>

                </flux:select>


                @error('form.categoria')

                <p class="text-red-400 text-sm mt-1">
                    {{ $message }}
                </p>

                @enderror


            </div>


            <div>

                <flux:label>
                    Prioridade de Atendimento
                </flux:label>


                <div class="grid grid-cols-4 gap-2 mt-1.5">


                    @foreach($this->priorityOptions as $value => $opt)

                        <button
                            type="button"
                            wire:click="$set('form.prioridade','{{ $value }}')"
                            class="text-sm font-medium py-2 rounded-lg border transition-colors
                            {{ $form->prioridade === $value
                                ? $opt['active']
                                : $opt['inactive'] }}"
                        >

                            {{ $opt['label'] }}

                        </button>

                    @endforeach


                </div>


                @error('form.prioridade')

                <p class="text-red-400 text-sm mt-1">
                    {{ $message }}
                </p>

                @enderror


            </div>


        </div>


        {{-- Descrição --}}
        <div>


            <flux:textarea
                label="Descrição Detalhada do Problema"
                rows="4"
                wire:model="form.descricao"
            />


            @error('form.descricao')

            <p class="text-red-400 text-sm mt-1">
                {{ $message }}
            </p>

            @enderror


        </div>


        {{-- Upload --}}
        <div>


            <flux:label>
                Anexar Documentos ou Capturas de Tela
            </flux:label>


            <label
                for="attachments"
                class="mt-1.5 flex flex-col items-center justify-center gap-2 border border-dashed border-zinc-700 rounded-xl py-8 cursor-pointer"
            >


                <flux:icon
                    name="arrow-up-tray"
                    class="w-5 h-5 text-zinc-300"
                />


                <p class="text-sm text-white">
                    Arraste seus arquivos ou procure no computador
                </p>


                <p class="text-xs text-zinc-500">
                    PNG, JPG, PDF, TXT ou ZIP (Máx. 10MB)
                </p>


                <input
                    id="attachments"
                    type="file"
                    multiple
                    wire:model="form.anexos"
                    class="hidden"
                />


            </label>


            @if($form->anexos)

                <div class="mt-3 space-y-2">


                    @foreach($form->anexos as $index => $file)

                        <div class="flex items-center justify-between bg-slate-900 rounded-lg px-3 py-2">


                            <span class="text-sm text-zinc-300 truncate">
                                {{ $file->getClientOriginalName() }}
                            </span>


                            <button
                                type="button"
                                wire:click="removeAttachment({{ $index }})"
                            >

                                <flux:icon
                                    name="x-mark"
                                    class="w-4 h-4 text-zinc-500"
                                />

                            </button>


                        </div>

                    @endforeach


                </div>

            @endif






            @error('form.anexos.*')


            <p class="text-red-400 text-sm mt-1">
                {{ $message }}
            </p>


            @enderror


        </div>


        {{-- Footer --}}

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-800">


            <flux:button
                type="button"
                variant="ghost"
                wire:click="close"
            >
                Cancelar
            </flux:button>


            <flux:button
                type="submit"
                variant="primary"
            >
                Criar Chamado
            </flux:button>


        </div>


    </form>


</div>
