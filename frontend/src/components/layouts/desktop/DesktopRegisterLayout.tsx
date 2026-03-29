import RegistrationForm from "@/components/forms/RegistrationForm";
import SplitScreenLayout from "../global/desktop/SplitScreenLayout";
import LoginImage from '@/assets/login/login-image-horizontal.webp'

export default function DesktopRegisterLayout() {
    return (
        <SplitScreenLayout backgroundImage={LoginImage}>
            <RegistrationForm />
        </SplitScreenLayout>
    )
}