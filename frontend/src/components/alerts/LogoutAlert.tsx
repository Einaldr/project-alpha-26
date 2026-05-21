import { SignOutIcon } from "@phosphor-icons/react";
import { AlertDialog, AlertDialogContent, AlertDialogHeader, AlertDialogTrigger } from "../ui/alert-dialog";
import { Button } from "../ui/button";

export default function LogoutAlert() {
    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>
                <Button variant="destructive">
                    <SignOutIcon />
                    <span>Logout</span>
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogHeader></AlertDialogHeader>
                </AlertDialogHeader>
            </AlertDialogContent>
        </AlertDialog>
    )
}