import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ChatLayout from '@/Layouts/ChatLayout';
import { ChatBubbleLeftRightIcon } from '@heroicons/react/24/solid';
import { Head } from '@inertiajs/react';
import React, { use, useEffect, useRef, useState } from 'react';
import ConversationHeader from '@/Components/App/ConversationHeader';
import MessageItem from '@/Components/App/MessageItem';
import MessageInput from '@/Components/App/MessageInput';
import { useEventBus } from '@/EventBus';

function Home({ selectedConversation=null, messages = null }) {
    const [localMessages, setLocalMessages] = useState([]);
    const messageCtrRef = useRef(null);
    const { on } = useEventBus();

    useEffect(() => {
        setTimeout(() => {
            if (messageCtrRef.current) {
                messageCtrRef.current.scrollTop = messageCtrRef.current.scrollHeight;
            }
        }, 100);
        const unsubscribe = on('message.created', (message) => {
            if(selectedConversation && selectedConversation.is_group && selectedConversation.id == message.group_id){
                setLocalMessages(prevMessages => [...prevMessages, message]);
            }
            if(selectedConversation && selectedConversation.is_user && (selectedConversation.id == message.sender_id
                 || selectedConversation.id == message.reciver_id)){
                setLocalMessages(prevMessages => [...prevMessages, message]);
            }
            
        });

        return () => {
            unsubscribe();
        };
    }, [selectedConversation]);

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
                <div
                    ref={messageCtrRef}
                    className="flex-1 overflow-y-auto p-5"
                >
                    {localMessages.length === 0 && (
                        <div className="flex justify-center items-center h-full">
                            <div className="text-lg text-slate-200">
                                No messages found
                            </div>
                        </div>
                    )}
                    {localMessages.length > 0 && (
                        <div className="flex-1 flex flex-col">
                            {/* <div ref={loadMoreIntersect}></div> */}
                            {localMessages.map((message) => (
                                <MessageItem
                                    key={message.id}
                                    message={message}
                                    // attachmentClick={onAttachmentClick}
                                />
                            ))}
                        </div>
                    )}
                </div>
                <MessageInput conversation={selectedConversation} />
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