import LoginForm from "@/components/forms/LoginForm";
import CardBasedLayout from "../global/mobile/CardBasedLayout";

export default function MobileLoginLayout() {
    return (
        <CardBasedLayout>
            <LoginForm />
        </CardBasedLayout>
    )
}