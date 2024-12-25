import { UserIcon } from "@heroicons/react/24/solid";

const GroupAvatar = ({}) => {
    return (
        <>
            <div className={`avatar paceholder`}>
                <div className={`bg-gray-200 text-gray-800 rounded-full w-8`}>
                    <span className="text-x1">
                        <UserIcon className="w-4 h-4 mr-2"/>
                    </span>
                </div>
            </div>
        </>
    )
}

export default GroupAvatar;