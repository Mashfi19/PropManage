
document.addEventListener("DOMContentLoaded", () => {
    const conversationItems = document.querySelectorAll(".conversation-item");
    const chatUserName = document.querySelector(".chat-user-name");
    const chatUserAvatar = document.querySelector(".chat-user-avatar");
    const chatMessages = document.querySelector(".chat-messages");
    const messageInput = document.getElementById("messageInput");
    const sendBtn = document.getElementById("sendMessageBtn");
    const searchInput = document.getElementById("conversationSearch");

    const chatHistory = {
        "Arafatullah Rifat": [
            { type: "received", text: "Hello! Is the 3-bed flat in Gulshan still available for rent?", time: "10:15 AM" },
            { type: "sent", text: "Hi Rifat, yes it is currently available and ready for viewing.", time: "10:18 AM" },
            { type: "received", text: "Great! Can I schedule a visit this Friday afternoon?", time: "10:20 AM" },
            { type: "sent", text: "Sure thing. 3:00 PM Friday works well. I'll have the keys ready.", time: "10:22 AM" },
            { type: "received", text: "Awesome, thank you! See you on Friday.", time: "10:25 AM" }
        ],
        "Fariha Akter Prity": [
            { type: "received", text: "Hi, I noticed the water faucet in Unit 3A has a slight leak.", time: "Yesterday" },
            { type: "sent", text: "Hello Fariha! Thanks for reporting. We have assigned maintenance technician to fix it tomorrow morning.", time: "Yesterday" },
            { type: "received", text: "Thank you so much for the quick response!", time: "Yesterday" }
        ],
        "Mashfi Rahman": [
            { type: "received", text: "I have just completed the monthly rent transfer via bKash.", time: "Aug 28" },
            { type: "sent", text: "Payment received and verified! Your digital receipt is ready on the portal.", time: "Aug 28" }
        ],
        "Md Farhan Nadim": [
            { type: "received", text: "Welcome to PropManage! Let me know if you need any assistance with lease documents.", time: "Aug 20" },
            { type: "sent", text: "Thank you! Everything looks great so far.", time: "Aug 20" }
        ]
    };

    conversationItems.forEach(item => {
        item.addEventListener("click", () => {
            conversationItems.forEach(c => c.classList.remove("active"));
            item.classList.add("active");

            const name = item.querySelector(".conversation-name").textContent.trim();
            const avatarSrc = item.querySelector("img").src;

            if (chatUserName) chatUserName.textContent = name;
            if (chatUserAvatar) chatUserAvatar.src = avatarSrc;

            renderMessages(name);
        });
    });

    function renderMessages(name) {
        if (!chatMessages) return;
        const messages = chatHistory[name] || [
            { type: "received", text: "Hello! How can I help you today?", time: "Just now" }
        ];

        let html = `<div class="message-date-divider"><span>Today</span></div>`;
        messages.forEach(msg => {
            const avatarUrl = msg.type === "received"
                ? (chatUserAvatar ? chatUserAvatar.src : "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80")
                : "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80";

            html += `
                <div class="message-row ${msg.type}">
                    <img class="message-avatar" src="${avatarUrl}" alt="Avatar">
                    <div class="message-content">
                        <div class="message-bubble">${escapeHTML(msg.text)}</div>
                        <div class="message-time">${msg.time}</div>
                    </div>
                </div>
            `;
        });

        chatMessages.innerHTML = html;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendMessage() {
        if (!messageInput) return;
        const text = messageInput.value.trim();
        if (!text) return;

        const currentContact = chatUserName ? chatUserName.textContent.trim() : "Arafatullah Rifat";
        if (!chatHistory[currentContact]) {
            chatHistory[currentContact] = [];
        }

        const now = new Date();
        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        chatHistory[currentContact].push({
            type: "sent",
            text: text,
            time: timeStr
        });

        messageInput.value = "";
        renderMessages(currentContact);

        const activeItem = document.querySelector(".conversation-item.active");
        if (activeItem) {
            const preview = activeItem.querySelector(".conversation-preview");
            if (preview) preview.textContent = "You: " + text;
            const timeEl = activeItem.querySelector(".conversation-time");
            if (timeEl) timeEl.textContent = timeStr;
        }

        setTimeout(() => {
            const responses = [
                "Received your message! I'll follow up shortly.",
                "Sounds good! I have made a note in the system.",
                "Thank you for the update!",
                "Got it! Let me know if anything else is needed."
            ];
            const botReply = responses[Math.floor(Math.random() * responses.length)];
            chatHistory[currentContact].push({
                type: "received",
                text: botReply,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });
            renderMessages(currentContact);
        }, 1200);
    }

    if (sendBtn) {
        sendBtn.addEventListener("click", sendMessage);
    }

    if (messageInput) {
        messageInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener("input", () => {
            const query = searchInput.value.toLowerCase();
            conversationItems.forEach(item => {
                const name = item.querySelector(".conversation-name").textContent.toLowerCase();
                item.style.display = name.includes(query) ? "flex" : "none";
            });
        });
    }

    function escapeHTML(str) {
        const div = document.createElement("div");
        div.innerText = str;
        return div.innerHTML;
    }
});
