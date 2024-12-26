import { Link, usePage } from "@inertiajs/react";
import { ArrowLeftIcon } from "@heroicons/react/24/solid";
import UserAvatar from "./UserAvatar";
import GroupAvatar from "./GroupAvatar";
import React from 'react';

const ConversationHeader = ({ selectedConversation }) => {
    if (!selectedConversation) {
        return null; // Return null if selectedConversation is undefined
    }

    return (
        <div className="conversation-header">
            <h2>{selectedConversation.name}</h2>
            {selectedConversation.is_user && (
                <p>User Conversation</p>
            )}
            {selectedConversation.is_group && (
                <p>Group Conversation</p>
            )}
        </div>
    );
};

export default ConversationHeader;