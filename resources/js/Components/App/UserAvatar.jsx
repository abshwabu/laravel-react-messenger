const UserAvatar = ({user, online = null, profile = false}) =>{
    let onlineClass = online === true ? 'online' : online === false ? 'offline' : '';
    const sizeClass = profile ? 'w-40' : 'w-8'

    console.log('status: ', onlineClass);
    

    return (
        <>
        {user.avatar_url && (
            <div className={`chat-image avatar ${onlineClass}`}>
                <div className={`rounded-full ${sizeClass}`}>
                    <img src={user.avatar_url} alt="" />
                </div>
            </div>
        )}
        {!user.avatar_url && (
            <div className={`chat-image avatar placeholder ${onlineClass}`}>
                <div className={`rounded-full bg-gray-400 text-gray-800 ${sizeClass}`}>
                    <span className="text-x1">
                        {user.name.substring(0,1)}
                    </span>
                </div>
            </div>
        )}
        </>
    )
}


export default UserAvatar
