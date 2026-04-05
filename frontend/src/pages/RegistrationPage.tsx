import RegistrationForm from "@/components/forms/RegistrationForm";
import SplitScreenLayout from "@/components/layouts/desktop/SplitScreenLayout";
import CardBasedLayout from "@/components/layouts/mobile/CardBasedLayout";
import BgImage from "@/assets/login/login-image-horizontal.webp";

export default function RegistrationPage() {
    return (
        <>
            <div className="block md:hidden">
                <CardBasedLayout><RegistrationForm /></CardBasedLayout>
            </div>

            <div className="hidden md:block">
                <SplitScreenLayout backgroundImage={BgImage}><RegistrationForm /></SplitScreenLayout>
            </div>
        </>
    )
}