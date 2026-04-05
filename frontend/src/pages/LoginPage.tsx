import CardBasedLayout from "@/components/layouts/mobile/CardBasedLayout";
import LoginForm from "@/components/forms/LoginForm";
import BgImage from "@/assets/login/login-image-horizontal.webp";
import SplitScreenLayout from "@/components/layouts/desktop/SplitScreenLayout";

export default function LoginPage() {
    return (
        <>
            <div className="block md:hidden">
                <CardBasedLayout><LoginForm /></CardBasedLayout>
            </div>

            <div className="hidden md:block">
                <SplitScreenLayout backgroundImage={BgImage}><LoginForm /></SplitScreenLayout>
            </div>
        </>
    )
}