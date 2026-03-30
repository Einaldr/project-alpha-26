import DesktopResetPasswordLayout from "@/components/layouts/desktop/DesktopForgotPasswordLayout";
import MobileResetPasswordLayout from "@/components/layouts/mobile/MobileResetPasswordLayout";
import { useViewType, VIEW_TYPES } from "@/hooks/useViewType";


export default function ResetPasswordPage() {
    const viewType = useViewType();

    if (viewType === VIEW_TYPES.MOBILE) {
        return <MobileResetPasswordLayout />
    }

    return <DesktopResetPasswordLayout />
}