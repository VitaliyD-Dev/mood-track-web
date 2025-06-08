<!-- Кнопка відкриття чату -->
<div id="chat-widget"
    class="mx-auto flex max-w-sm items-center gap-x-4 rounded-xl bg-white p-6 shadow-lg outline outline-black/5 dark:bg-slate-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10 cursor-pointer animate-pulsate transform hover:bg-blue-700 transition"
    style="position: fixed; bottom: 10rem; right: 1rem; z-index: 9999;">
    <img class="size-12 shrink-0" src="/img/logo.png" alt="Chat Logo" />
    <div>
        <div class="text-xl font-medium text-black dark:text-black">Чат з Llama 3</div>
        <p class="text-gray-500 dark:text-gray-400">Натисніть щоб відкрити чат</p>
    </div>
</div>

<!-- Блок чату -->
<div id="chat-box"
    class="fixed bottom-20 w-96 bg-white shadow-xl rounded-lg hidden z-50 transition-all duration-300 ease-in-out opacity-0 scale-95 dark:bg-slate-800"
    style="right: 0.5rem !important; bottom: 8rem !important; width: 384px; height: 480px;">
    <div class="bg-blue-600 text-white p-4 rounded-t-lg flex justify-between items-center">
        <div class="flex items-center gap-x-3">
            <img class="size-8 shrink-0" src="/img/logo.png" alt="Chat Logo" />
            <span class="font-bold">Чат з Llama 3</span>
        </div>
        <button id="close-chat" class="text-lg focus:outline-none">✖</button>
    </div>
    <div class="p-4 h-[calc(100%-48px)] overflow-y-auto text-black dark:text-black-300" style="word-break: break-word;">
        <div class="flex flex-col h-full">
            <div id="chat-messages" class="flex-1 overflow-y-auto mb-2 text-sm"></div>

            <div class="flex">
                <input type="text" id="chat-input" placeholder="Введіть повідомлення..."
                    class="flex-1 border rounded-l px-3 py-2 focus:outline-none text-black dark:bg-slate-700 dark:border-slate-600 dark:text-black">
                <button id="chat-send" class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700">
                    Надіслати
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <style>
        /* Приховуємо скролбар для WebKit (Chrome, Safari) */
        #chat-box .overflow-y-auto::-webkit-scrollbar {
            display: none;
        }

        /* Приховуємо скролбар для IE, Edge та Firefox */
        #chat-box .overflow-y-auto {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
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
                const messages = Array.from(chatMessages.children).map(messageElement => {
                    const senderEmoji = messageElement.querySelector('strong').textContent.replace(':', '').trim();
                    // Отримуємо текст повідомлення зі span
                    const textElement = messageElement.querySelector('span');
                    const text = textElement ? textElement.textContent.trim() : ''; // Завжди беремо текст зі span
                    
                    return { emoji: senderEmoji, text: text };
                });
                sessionStorage.setItem('chatHistory', JSON.stringify(messages));
            }

            // Функція для завантаження повідомлень з sessionStorage
            function loadMessages() {
                const savedMessages = sessionStorage.getItem('chatHistory');
                if (savedMessages) {
                    let messages;
                    try {
                        messages = JSON.parse(savedMessages);
                    } catch (e) {
                         console.error('Failed to parse chat history from sessionStorage:', e);
                         sessionStorage.removeItem('chatHistory'); // Очистити некоректні дані
                         return;
                    }

                    messages.forEach(message => {
                        let cleanText = message.text;
                        // Спробувати видалити дублікати префіксів, які могли зберегтися
                        const prefixes = ['👤:', '🤖:', '⚠️:'];
                        let prefixFound = true;
                        while(prefixFound) {
                            prefixFound = false;
                            for(const prefix of prefixes) {
                                if (cleanText.startsWith(prefix)) {
                                    cleanText = cleanText.substring(prefix.length).trim();
                                    prefixFound = true; // Продовжити перевірку, якщо знайдено префікс
                                    break; // Вийти з внутрішнього циклу і перевірити знову
                                }
                            }
                        }

                        // Передаємо очищений текст в appendMessage з save = false
                        appendMessage(message.emoji, cleanText, false);
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
                const message = chatInput.value.trim();
                if (!message) return;

                appendMessage('👤', message);
                chatInput.value = '';

                // Додаємо повідомлення бота з тимчасовим текстом для ефекту набору
                const botMessageDiv = appendMessage('🤖', '', false); // Передаємо false, щоб не зберігати одразу

                try {
                    const response = await fetch('/api/llama/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: message,
                            model: 'llama3'
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Network response was not ok');
                    }

                    // Запускаємо ефект набору тексту для відповіді бота
                    await typeMessage(botMessageDiv, data.response);

                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    saveMessages(); // Зберігаємо повідомлення після завершення набору

                } catch (error) {
                    console.error('Error:', error);
                    // Якщо сталася помилка, оновлюємо останнє повідомлення бота з помилкою
                    if (botMessageDiv) {
                        // Отримуємо span для помилки, якщо він є (має бути після appendMessage)
                        const typingTextSpan = botMessageDiv.querySelector('span');
                        if (typingTextSpan) {
                             typingTextSpan.textContent = `Помилка: ${error.message}`;
                             typingTextSpan.removeAttribute('id'); // Видаляємо id після оновлення
                        } else {
                             // Якщо span чомусь не знайдено, просто оновлюємо innerHTML
                            botMessageDiv.innerHTML = `<strong>⚠️:</strong> Помилка: ${error.message}`;
                        }

                    } else {
                         // Якщо botMessageDiv не був створений взагалі (дуже малоймовірно)
                         appendMessage('⚠️', `Помилка: ${error.message}`);
                    }
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    saveMessages(); // Зберігаємо повідомлення про помилку
                }
            }

            // Функція для додавання повідомлення в чат
            function appendMessage(senderEmoji, text, save = true) {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('my-1');
                 // Використовуємо <span> для вмісту, що набирається
                messageDiv.innerHTML = `<strong>${senderEmoji}:</strong> <span>${text}</span>`;
                chatMessages.appendChild(messageDiv);
                
                if (save) {
                    saveMessages();
                }
                return messageDiv; // Повертаємо створений елемент для ефекту набору
            }

            // Функція для імітації набору тексту
            async function typeMessage(messageElement, text) {
                // Шукаємо span для набору тексту всередині елемента повідомлення
                const typingTextSpan = messageElement.querySelector('span');
                if (!typingTextSpan) {
                     console.error('Typing span not found!', messageElement);
                     return;
                }
                
                typingTextSpan.textContent = ''; // Очищаємо початковий текст
                const characters = text.split('');

                for (let i = 0; i < characters.length; i++) {
                    typingTextSpan.textContent += characters[i];
                    chatMessages.scrollTop = chatMessages.scrollHeight; // Прокручуємо вниз під час набору
                    await new Promise(resolve => setTimeout(resolve, 20)); // Затримка між символами (20 мс)
                }
            }

            // Зберігаємо історію при закритті вікна
            window.addEventListener('beforeunload', saveMessages);
        });
    </script>
@endpush