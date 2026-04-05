import type { ReactNode } from "react"

interface SplitScreenLayoutProps {
    backgroundImage: string
    children: ReactNode
}

const SplitScreenLayout = ({ backgroundImage, children }: SplitScreenLayoutProps) => {
    return (
        <main className='flex flex-row min-h-screen w-full'>
            <section className="absolute flex min-h-screen items-center justify-center w-4/5 md:w-3/4 lg:1/2 xl:w-1/3 p-12 bg-background overflow-hidden z-10">
                <div className="absolute -top-[10%] -left-[10%] w-72 h-72 bg-purple-500/20 rounded-full blur-[75px]" />
                <div className="absolute -bottom-[10%] -right-[10%] w-94 h-94 bg-blue-500/10 rounded-full blur-[75px]" />

                {children}
            </section>
            <aside className="relative block w-full aria-hidden z-0">
                <img
                    src={backgroundImage}
                    alt='Background'
                    className="absolute inset-0 w-full h-full object-cover z-0" />
            </aside>
        </main>
    )
}

export default SplitScreenLayout