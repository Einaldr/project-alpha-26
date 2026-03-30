import DesktopForgotPasswordLayout from "@/components/layouts/desktop/DesktopForgotPasswordLayout";
import MobileForgotPasswordLayout from "@/components/layouts/mobile/MobileForgotPasswordLayout";
import { useViewType, VIEW_TYPES } from "@/hooks/useViewType";

export default function ForgotPasswordPage() {
    const viewType = useViewType();
    
    if (viewType === VIEW_TYPES.MOBILE) {
        return <MobileForgotPasswordLayout />
    }

    return <DesktopForgotPasswordLayout />
}