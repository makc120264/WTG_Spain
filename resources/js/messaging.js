export function initMessaging(config) {
    const { authUserId, messagesContainerId, messageFormId, formErrorId, storeUrl } = config;

    const messagesContainer = document.getElementById(messagesContainerId);
    const messageForm = document.getElementById(messageFormId);
    const formError = document.getElementById(formErrorId);

    if (!messagesContainer || !messageForm) return;

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
            const response = await window.axios.post(storeUrl, {
                recipient_id: formData.get('recipient_id'),
                body: formData.get('body'),
            });

            messageForm.reset();
        } catch (error) {
            formError.textContent = error.response?.data?.message ?? 'Ошибка отправки сообщения';
        }
    });

    if (window.Echo && authUserId) {
        console.log('Initializing Echo for user', authUserId);
        window.Echo.private(`users.${authUserId}`)
            .listen('.message.sent', (event) => {
                console.log('Message received:', event);
                renderMessage(event);
            });
    } else {
        console.warn('Echo or authUserId not available', { echo: !!window.Echo, authUserId });
    }
}
