import { useViewType, VIEW_TYPES } from "@/hooks/useViewType"
import MobileLoginLayout from "@/components/layouts/mobile/MobileLoginLayout";
import DesktopLoginLayout from "@/components/layouts/desktop/DesktopLoginLayout";

export default function LoginPage() {
    const viewType = useViewType();

    if (viewType == VIEW_TYPES.MOBILE || viewType == VIEW_TYPES.TABLET) {
        return <MobileLoginLayout />
    }

    return <DesktopLoginLayout />
}