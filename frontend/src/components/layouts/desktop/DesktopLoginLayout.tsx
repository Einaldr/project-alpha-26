import LoginForm from '@/components/forms/LoginForm'
import LoginImage from '../../../assets/login/login-image-horizontal.webp'
import SplitScreenLayout from '../global/desktop/SplitScreenLayout'

export default function DesktopLoginLayout() {
    return (
        <SplitScreenLayout backgroundImage={LoginImage}>
            <LoginForm />
        </SplitScreenLayout>
    )
}