import DesktopRegisterLayout from "@/components/layouts/desktop/DesktopRegisterLayout";
import MobileRegisterLayout from "@/components/layouts/mobile/MobileRegisterLayout";
import { useViewType, VIEW_TYPES } from "@/hooks/useViewType";

export default function RegistrationPage() {
    const viewType = useViewType();

    if (viewType === VIEW_TYPES.MOBILE) {
        return <MobileRegisterLayout />
    }

    return <DesktopRegisterLayout />
}