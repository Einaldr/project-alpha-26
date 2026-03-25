import LoginForm from '@/components/forms/LoginForm'
import LoginImage from '../../../assets/login/login-image-horizontal.webp'

export default function DesktopLoginLayout() {
    return (
        <main className='flex flex-row min-h-screen w-full'>
            <section className="relative flex min-h-screen items-center justify-center w-1/3 p-12 bg-background/60 overflow-hidden">
                <div className="absolute top-[-10%] left-[-10%] w-72 h-72 bg-purple-500/20 rounded-full blur-[100px]" />
                <div className="absolute bottom-[10%] right-[-10%] w-94 h-94 bg-blue-500/10 rounded-full blur-[100px]" />

                <LoginForm />
            </section>
            <aside className="relative block w-full aria-hidden z-0">
                <img 
                    src={LoginImage}
                    alt='Background' 
                    className="absolute inset-0 w-full h-full object-cover z-0"/>
            </aside>
        </main>
    )
}