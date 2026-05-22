import { SignOutIcon } from "@phosphor-icons/react";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogMedia, AlertDialogTitle, AlertDialogTrigger } from "../ui/alert-dialog";
import { Button } from "../ui/button";

export default function LogoutAlert() {
    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>
                <Button variant="destructive" className="w-full">
                    <SignOutIcon className="mr-auto"/>
                    <span>Logout</span>
                    <SignOutIcon className="ml-auto"/>
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogHeader>
                        <AlertDialogMedia>
                            <SignOutIcon />
                        </AlertDialogMedia>
                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                    </AlertDialogHeader>
                    <AlertDialogDescription>
                        Are you sure you want to log out?
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Stay</AlertDialogCancel>
                    <AlertDialogAction>Logout</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    )
}