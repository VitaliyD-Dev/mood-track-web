<!-- Кнопка відкриття чату -->
<div id="chat-widget"
    class="bg-blue-600 text-white px-4 py-2 rounded-full shadow-lg cursor-pointer animate-pulsate transform hover:bg-blue-700 transition"
    style="position: fixed; bottom: 10rem; right: 1rem; z-index: 9999;">
    💬 Чат
</div>

<!-- Блок чату -->
<div id="chat-box"
    class="fixed bottom-20 w-80 h-96 bg-white shadow-xl rounded-lg hidden z-50 transition-all duration-300 ease-in-out opacity-0 scale-95"
    style="right: 0.5rem !important; bottom: 8rem !important;">
    <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
        <span class="font-bold">Чат</span>
        <button id="close-chat" class="text-lg focus:outline-none">✖</button>
    </div>
    <div class="p-4 h-[calc(100%-48px)] overflow-y-auto text-gray-600">
        <div class="flex flex-col h-full">
            <div id="chat-messages" class="flex-1 overflow-y-auto mb-2 text-sm"></div>

            <div class="flex">
                <input type="text" id="chat-input" placeholder="Введіть повідомлення..."
                    class="flex-1 border rounded-l px-3 py-2 focus:outline-none">
                <button id="chat-send" class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700">
                    Надіслати
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatWidget = document.getElementById('chat-widget');
            const chatBox = document.getElementById('chat-box');
            const closeChat = document.getElementById('close-chat');
            const chatInput = document.getElementById('chat-input');
            const chatSend = document.getElementById('chat-send');
            const chatMessages = document.getElementById('chat-messages');

            // Функція для збереження повідомлень в sessionStorage
            function saveMessages() {
                const messages = Array.from(chatMessages.children).map(message => ({
                    emoji: message.querySelector('strong').textContent,
                    text: message.textContent.replace(message.querySelector('strong').textContent, '').trim()
                }));
                sessionStorage.setItem('chatHistory', JSON.stringify(messages));
            }

            // Функція для завантаження повідомлень з sessionStorage
            function loadMessages() {
                const savedMessages = sessionStorage.getItem('chatHistory');
                if (savedMessages) {
                    const messages = JSON.parse(savedMessages);
                    messages.forEach(message => {
                        appendMessage(message.emoji, message.text, false);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }

            // Завантажуємо збережені повідомлення при завантаженні сторінки
            loadMessages();

            chatWidget.addEventListener('click', () => {
                if (chatBox.classList.contains('hidden')) {
                    chatBox.classList.remove('hidden');
                    setTimeout(() => {
                        chatBox.classList.remove('opacity-0', 'scale-95');
                        chatBox.classList.add('opacity-100', 'scale-100');
                    }, 10);
                    chatWidget.style.zIndex = '49';
                } else {
                    chatBox.classList.remove('opacity-100', 'scale-100');
                    chatBox.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        chatBox.classList.add('hidden');
                    }, 300);
                    chatWidget.style.zIndex = '9999';
                }
            });

            closeChat.addEventListener('click', () => {
                chatBox.classList.remove('opacity-100', 'scale-100');
                chatBox.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    chatBox.classList.add('hidden');
                }, 300);
                chatWidget.style.zIndex = '9999';
            });

            chatSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });

            async function sendMessage() {
                const prompt = chatInput.value.trim();
                if (!prompt) return;

                appendMessage('👤', prompt);
                chatInput.value = '';

                try {
                    const instruction = "Ти — експерт з технічної підтримки. Відповідай коротко й офіційно. Ти відповідаєш лише на запитання про роботу сайту та форуми. Ти - консультант треба спочатку дізнатись, що користувач хоче в тебе запитати. Ти маєш відповідати мовою якою користувач пише тобі повідомлення";

                    const response = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            prompt: prompt,
                            instruction: instruction
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    appendMessage('🤖', data.response);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } catch (error) {
                    appendMessage('⚠️', 'Помилка при надсиланні повідомлення.');
                }
            }

            function appendMessage(senderEmoji, text, save = true) {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('my-1');
                messageDiv.innerHTML = `<strong>${senderEmoji}:</strong> ${text}`;
                chatMessages.appendChild(messageDiv);
                
                if (save) {
                    saveMessages();
                }
            }

            // Зберігаємо історію при закритті вікна
            window.addEventListener('beforeunload', saveMessages);
        });
    </script>
@endpush