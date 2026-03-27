import { type IconProps, BarricadeIcon, ClockUserIcon, LockKeyIcon, CloudXIcon } from "@phosphor-icons/react";
import { AlertDialog, AlertDialogAction, AlertDialogContent, AlertDialogDescription, AlertDialogHeader, AlertDialogMedia, AlertDialogTitle } from "@/components/ui/alert-dialog";
import { Separator } from "../ui/separator";

export type AuthVariant = "expired" | "unauthenticated" | "forbidden" | "serverFailure";

interface AuthAlertProps {
    variant: AuthVariant
    isOpen: boolean
    onClose?: () => void
    onLogin?: () => void
}

const VARIANT_CONFIG: Record<AuthVariant, {
    title: string
    description: string
    actionText: string
    icon: React.ElementType<IconProps>
    iconColor: string
}> = {
    expired: {
        title: "Session Expired",
        description: "For your security, you have been logged out. Please sign back in.",
        actionText: "Log in",
        icon: ClockUserIcon,
        iconColor: "color-primary",
    },
    unauthenticated: {
        title: "Login Required",
        description: "You need to be logged in to access this page or perform this action.",
        actionText: "Log in",
        icon: LockKeyIcon,
        iconColor: "color-primary",
    },
    forbidden: {
        title: "Access Denied",
        description: "You do not have the necessary permissions to view this resource.",
        actionText: "Go back",
        icon: BarricadeIcon,
        iconColor: "color-destructive",
    },
    serverFailure: {
        title: "Server Failure",
        description: "Server Error",
        actionText: "Go to site status",
        icon: CloudXIcon,
        iconColor: "color-destructive",
    }
}

export const AuthAlert = ({ variant, isOpen, onClose, onLogin }: AuthAlertProps) => {
    const { title, description, actionText, icon: Icon, iconColor } = VARIANT_CONFIG[variant];

    return (
        <>
            <div className="min-w-screen min-h-screen">
            <div className="absolute -top-[15%] -left-[10%] lg:-top-[10%] lg:-left-[5%] w-64 h-64 lg:w-94 lg:h-94 bg-purple-500/30 lg:bg-purple-500/20 rounded-full blur-[100px] lg:blur-[200px]" />
            <div className="absolute -bottom-[15%] -right-[10%] lg:-bottom-[10%] lg:-right-[5%] w-64 h-64 lg:w-94 lg:h-94 bg-blue-500/20 lg:bg-blue-500/10 rounded-full blur-[100px] lg:blur-[200px]" />
            </div>
            <AlertDialog open={isOpen} onOpenChange={onClose}>
                <AlertDialogContent size="sm" className="border border-border">
                    <AlertDialogHeader>
                        <AlertDialogMedia>
                            <Icon size={32} weight="bold" className={iconColor} />
                        </AlertDialogMedia>
                        <AlertDialogTitle className="pb-2">{title}</AlertDialogTitle>
                        <AlertDialogDescription className="pb-2">
                            {description}
                        </AlertDialogDescription>
                        <Separator />
                        <div className="p-1" />
                        <AlertDialogAction variant="default" onClick={onLogin} className="w-2/3">{actionText}</AlertDialogAction>
                    </AlertDialogHeader>
                </AlertDialogContent>
            </AlertDialog>
        </>
    )
}