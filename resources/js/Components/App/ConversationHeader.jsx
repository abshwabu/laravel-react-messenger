import UserAvatar from './UserAvatar';
import GroupAvatar from './GroupAvatar';
import { Link } from '@inertiajs/react';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';

const ConversationHeader = ({ selectedConversation }) => {
    console.log('Selected Conversation:', selectedConversation);
    
    // Early return if no valid conversation
    if (!selectedConversation || !selectedConversation.id || !selectedConversation.name) {
        return null;
    }
    
    return (
        <div className="p-3 flex justify-between items-center border-b border-slate-700">
            <div className="flex items-center gap-3">
                <Link
                    href={route("dashboard")}
                    className="inline-block sm:hidden"
                >
                    <ArrowLeftIcon className="w-6" />
                </Link>
                {selectedConversation.is_user && (
                    <UserAvatar user={selectedConversation} />
                )}
                {selectedConversation.is_group && selectedConversation.id && (
                    <GroupAvatar />
                )}
                <div>
                    <h3 className="text-sm font-semibold">
                        {selectedConversation.name}
                    </h3>
                    {selectedConversation.is_group && selectedConversation.users && (
                        <p className="text-xs text-gray-500">
                            {selectedConversation.users.length} members
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ConversationHeader;