import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ChatLayout from '@/Layouts/ChatLayout';
import { ChatBubbleLeftRightIcon } from '@heroicons/react/24/solid';
import { Head } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import ConversationHeader from '@/Components/App/ConversationHeader';
import MessageItem from '@/Components/App/MessageItem';

function Home({ selectedConversation = null, messages = null }) {
    console.log('messages', messages);
    
    const [localMessages, setLocalMessages] = useState([]);
    const messageCtrRef = React.useRef(null);
    useEffect(() => {
        setLocalMessages(messages ? messages.data.reverse() : []);
    }, [messages]);

    return (
        <>
            {!messages && (
                <div className="flex flex-col gap-8 justify-center items-center text-center opacity-35 h-full">
                    <div className="text-2xl md:text-4xl text-slate-200">
                        Please Select Conversation to see Message
                    </div>
                    <ChatBubbleLeftRightIcon className="w-32 h-32 inline-block" />
                </div>
            )}
            {messages && (
                <>
                    <ConversationHeader 
                        selectedConversation={selectedConversation}
                    />
                    <div ref={messageCtrRef} className="overflow-auto flex flex-col flex-1">
                        {localMessages.map((message) => (
                            <MessageItem key={message.id} message={message} />
                        ))}
                    </div>
                </>
            )}
        </>
    );
}

Home.layout = (page) => (
    <AuthenticatedLayout
    user={page.props.user}
    > 
        
        <ChatLayout children={page}/>
    </AuthenticatedLayout>
);

export default Home;