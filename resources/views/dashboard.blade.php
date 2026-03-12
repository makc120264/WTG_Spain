<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Список пользователей</h3>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($users as $user)
                                <div class="border rounded p-3 text-sm">
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-gray-500">{{ $user->email }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-3">Отправить сообщение</h3>
                        <form id="message-form" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm mb-1" for="recipient_id">Получатель</label>
                                <select id="recipient_id" name="recipient_id" class="border rounded w-full p-2" required>
                                    <option value="">Выберите пользователя</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm mb-1" for="body">Текст</label>
                                <textarea id="body" name="body" rows="3" class="border rounded w-full p-2" required></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Отправить</button>
                        </form>
                        <p id="form-error" class="text-red-600 text-sm mt-2"></p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-3">Входящие сообщения</h3>
                        <div id="messages" class="space-y-2">
                            @forelse($incomingMessages as $message)
                                <div class="border rounded p-3 text-sm">
                                    <div class="font-medium">От: {{ $message->sender->name }}</div>
                                    <div>{{ $message->body }}</div>
                                    <div class="text-gray-500 text-xs mt-1">{{ $message->created_at->format('Y-m-d H:i:s') }}</div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500" id="empty-messages">Сообщений пока нет</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const authUserId = {{ auth()->id() }};
        const messagesContainer = document.getElementById('messages');
        const messageForm = document.getElementById('message-form');
        const formError = document.getElementById('form-error');

        const renderMessage = (payload) => {
            const empty = document.getElementById('empty-messages');

            if (empty) {
                empty.remove();
            }

            const item = document.createElement('div');
            item.className = 'border rounded p-3 text-sm';
            item.innerHTML = `
                <div class="font-medium">От: ${payload.sender_name}</div>
                <div>${payload.body}</div>
                <div class="text-gray-500 text-xs mt-1">${payload.created_at}</div>
            `;

            messagesContainer.appendChild(item);
        };

        messageForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            formError.textContent = '';

            const formData = new FormData(messageForm);

            try {
                const response = await window.axios.post('{{ route('messages.store') }}', {
                    recipient_id: formData.get('recipient_id'),
                    body: formData.get('body'),
                });

                messageForm.reset();
            } catch (error) {
                formError.textContent = error.response?.data?.message ?? 'Ошибка отправки сообщения';
            }
        });

        window.Echo.private(`users.${authUserId}`)
            .listen('.message.sent', (event) => {
                renderMessage(event);
            });
    </script>
</x-app-layout>
