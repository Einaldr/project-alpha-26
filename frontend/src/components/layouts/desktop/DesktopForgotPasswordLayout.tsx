import ForgotPasswordForm from "@/components/forms/ForgotPasswordForm";
import SplitScreenLayout from "../global/desktop/SplitScreenLayout";
import BgImage from "@/assets/login/login-image-horizontal.webp";

export default function DesktopForgotPasswordLayout() {
    return (
        <SplitScreenLayout backgroundImage={BgImage}>
            <ForgotPasswordForm />
        </SplitScreenLayout>
    )
}