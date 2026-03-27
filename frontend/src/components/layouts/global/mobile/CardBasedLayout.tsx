import type { ReactNode } from "react";

interface CardBasedLayoutProps {
    children: ReactNode
}

const CardBasedLayout = ({children }: CardBasedLayoutProps) => {
    return (
        <main className='relative w-full min-h-dvh flex flex-col items-center justify-center p-4 bg-background overflow-hidden'>
            <div className="absolute top-[-10%] left-[-10%] w-72 h-72 bg-purple-500/20 rounded-full blur-[100px]" />
            <div className="absolute bottom-[10%] right-[-10%] w-94 h-94 bg-blue-500/10 rounded-full blur-[100px]" />
            <section className="w-full h-2/3 p-4 z-10">
                {children}
            </section>
        </main>
    )
}

export default CardBasedLayout;