import { Input } from '@/components/ui/input'
import { Field, FieldError, FieldGroup, FieldLabel } from '../ui/field'
import { Button } from '@/components/ui/button'
import { toast } from 'sonner'
import { useNavigate, Link } from 'react-router-dom'
import { z } from 'zod'
import { standardSchemaResolver } from '@hookform/resolvers/standard-schema'
import { Controller, useForm } from 'react-hook-form'
import api from '@/lib/api.ts'


const loginFormSchema = z.object({
    email: z.email(),
    password: z.string()
});

export default function LoginForm() {
    const navigate = useNavigate();

    const form = useForm<z.infer<typeof loginFormSchema>>({
        resolver: standardSchemaResolver(loginFormSchema),
        defaultValues: {
            email: "",
            password: ""
        }
    })

    async function onSubmit(data: z.infer<typeof loginFormSchema>) {
        const loginWithDelay = async () => {
            const apiCall = api.post('/auth/login', data);
            const timer = new Promise((resolve) => setTimeout(resolve, 1000));

            const [response] = await Promise.all([apiCall, timer]);

            return response.data;
        };

        toast.promise(loginWithDelay, {
            loading: 'Verifying credentials...',
            success: (data) => {

                localStorage.setItem('token', data.token);

                setTimeout(() => navigate('/dashboard'), 500);

                return 'Welcome back, ' + data.user.name + '!';
            },
            error: (err) => {
                return err?.response?.data?.message || 'Something went wrong';
            }
        })
    }

    return (
        <div className='relative flex flex-col w-full h-full items-center'>

            <h1 className="text-4xl text-foreground m-16 font-extrabold">Welcome!</h1>

            <form className="flex flex-col w-full items-center" onSubmit={form.handleSubmit(onSubmit)}>

                <FieldGroup className='p-8 border border-border rounded-sm'>

                    <Controller name='email' control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldLabel htmlFor='fieldgroup-email'>Email</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='text' id='fieldgroup-email' placeholder='name@example.com' autoComplete='email' required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />

                    <Controller name='password' control={form.control} render={({ field, fieldState }) => (
                        <Field>
                            <FieldLabel htmlFor='filegroup-password'>Password</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='password' id='fieldgroup-password' placeholder='************' className='' autoComplete='current-password' required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                            <div className='flex justify-end'>
                                <Link to='/forgot-password' className='hover:text-foreground text-muted-foreground hover:underline text-xs'>Forgot password</Link>
                            </div>
                        </Field>
                    )} />


                    <Field>
                        <Button size='lg' type='submit' className='w-full'>Login</Button>
                    </Field>

                </FieldGroup>
            </form>

            <div className="w-full p-8">

                <div className="relative flex py-5 items-center">
                    <div className="grow border-t border-border"></div>

                    <span className="shrink mx-4 text-muted-foreground text-sm uppercase">Or</span>

                    <div className="grow border-t border-border"></div>
                </div>


                <Button size='lg' variant='secondary' className='w-full ' onClick={() => { navigate('/register') }}>Register</Button>
            </div>
        </div>
    )
}