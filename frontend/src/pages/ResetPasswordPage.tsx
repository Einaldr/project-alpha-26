import ResetPasswordForm from "@/components/forms/ResetPasswordForm";
import SplitScreenLayout from "@/components/layouts/desktop/SplitScreenLayout";
import CardBasedLayout from "@/components/layouts/mobile/CardBasedLayout";
import BgImage from "@/assets/login/login-image-horizontal.webp";


export default function ResetPasswordPage() {
    return (
        <>
            <div className="block md:hidden">
                <CardBasedLayout>
                    <ResetPasswordForm />
                </CardBasedLayout>
            </div>

            <div className="hidden md:block">
                <SplitScreenLayout backgroundImage={BgImage}>
                    <ResetPasswordForm />
                </SplitScreenLayout>
            </div>
        </>
    )
}