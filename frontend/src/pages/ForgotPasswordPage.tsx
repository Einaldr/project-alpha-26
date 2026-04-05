import CardBasedLayout from "@/components/layouts/mobile/CardBasedLayout";
import ForgotPasswordForm from "@/components/forms/ForgotPasswordForm";
import BgImage from "@/assets/login/login-image-horizontal.webp";
import SplitScreenLayout from "@/components/layouts/desktop/SplitScreenLayout";

export default function ForgotPasswordPage() {
    return (
        <>
            <div className="block md:hidden">
                <CardBasedLayout>
                    <ForgotPasswordForm />
                </CardBasedLayout>
            </div>

            <div className="hidden md:block">
                <SplitScreenLayout backgroundImage={BgImage}>
                    <ForgotPasswordForm />
                </SplitScreenLayout>
            </div>
        </>
    )
}