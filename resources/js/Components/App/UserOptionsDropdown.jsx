import { Menu, MenuItem, MenuItems, Transition } from "@headlessui/react";
import { EllipsisVerticalIcon, LockClosedIcon, LockOpenIcon, ShieldCheckIcon, UserIcon } from "@heroicons/react/24/solid";
import axios from "axios";
import { Fragment } from "react";

export default function UserOptionsDropdown ({conversation})  {
    const changeUserRole = () => {
        console.log('Change User Role!');
        
        if (!conversation.is_user) {
            return;
        }

        axios
            .post(route("user.changeRole", conversation.id))
            .then((res)=>{
                console.log(res.data)
            })
            .catch((err)=>{
                console.error(err);
                
            })
    }
    const onBlockUser = () => {
        console.log('Block User');
        
        if (!conversation.is_user) {
            return;
        }

        axios
            .post(route(`user.blockUnblock`, conversation.id))
            .then((res) => {
                console.log(res.data);
            })
            .catch((err) => {
                console.error(err);
            });
    }

    return (
        <Menu as="div" className="relative inline-block text-left">
            <div>
                <Menu.Button className="flex item-center justify-center w-8 h-8 rounded-full hover:bg-black/40">
                    <EllipsisVerticalIcon className="w-5 h-5" aria-hidden="true" />
                </Menu.Button>
            </div>
            <Transition
                as={Fragment}
                enter="transition ease-out duration-100"
                enterFrom="transform opacity-0 scale-95"
                enterTo="transform opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="transform opacity-100 scale-100"
                leaveTo="transform opacity-0 scale-95"
            >
                <MenuItems className="absolute right-0 mt-2 w-48 rounded-md bg-black text-white shadow-lg z-50">
                    <div className="py-1">
                        <MenuItem as="button" onClick={changeUserRole} className="block px-4 py-2 text-sm bg-black/30 text-white">
                            {({ active }) => (
                                <span className={active ? 'bg-gray-100' : ''}>
                                    <UserIcon className="w-5 h-5 mr-2" aria-hidden="true" />
                                    Change Role
                                </span>
                            )}
                        </MenuItem>
                        <MenuItem as="button" onClick={onBlockUser} className="block px-4 py-2 text-sm bg-black/30 text-white">
                            {({ active }) => (
                                <span className={active ? 'bg-gray-100' : ''}>
                                    {conversation.blocked_at ? (
                                        <>
                                            <LockOpenIcon className="w-5 h-5 mr-2" aria-hidden="true" />
                                            Unblock User
                                        </>
                                    ) : (
                                        <>
                                            <LockClosedIcon className="w-5 h-5 mr-2" aria-hidden="true" />
                                            Block User
                                        </>
                                    )}
                                </span>
                            )}
                        </MenuItem>
                    </div>
                </MenuItems>
            </Transition>
        </Menu>
    );
}