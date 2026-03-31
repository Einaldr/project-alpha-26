import z from "zod";
import { FieldGroup, FieldLabel, Field, FieldError, FieldDescription, FieldContent } from "../ui/field";
import { useNavigate } from "react-router-dom";
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema";
import { Controller, useForm } from "react-hook-form";
import { Input } from "../ui/input";
import { Button } from "../ui/button";
import api from "@/lib/api";
import { toast } from "sonner";


const ForgotPasswordSchema = z.object({
    email: z.email()
});

export default function ForgotPasswordForm() {
    const navigate = useNavigate();

    const form = useForm<z.infer<typeof ForgotPasswordSchema>>({
        resolver: standardSchemaResolver(ForgotPasswordSchema),
        defaultValues: {
            email: "",
        }
    });


    async function onSubmit(data: z.infer<typeof ForgotPasswordSchema>) {
        const forgotPasswordWithDelay = async () => {
            const apiCall = api.post('/auth/password/forgot-password', {
                email: data.email,
            });

            const timer = new Promise((resolve) => setTimeout(resolve, 1000));

            const [response] = await Promise.all([apiCall, timer]);

            return response.data;
        };

        toast.promise(forgotPasswordWithDelay, {
            loading: 'Sending link...',
            success: 'Link sent! Open your mailbox to continue.',
            error: (err) => {
                return err?.response?.data?.message || 'Something went wrong';
            }
        })
    }

    return (
        <div className='relative flex flex-col w-full h-full items-center'>
            <form className='flex flex-col w-full items-center border border-border rounded-sm' onSubmit={form.handleSubmit(onSubmit)}>
                <FieldGroup className='p-4 pr-8 pl-8 rounded-sm mb-2'>
                    <Controller name='email' control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldContent>
                                <FieldDescription className="text-center text-foreground text-sm">Enter your user account's verified email address and we will send you a password reset link.</FieldDescription>
                            </FieldContent>
                            <FieldLabel htmlFor="fieldgroup-email">Email</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='email' id='fieldgroup-email' placeholder="example@email.com" autoComplete="email" required/>
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />

                    <Field className="p-4">
                        <Button type='submit' variant='default' size='lg'>Send Link</Button>
                    </Field>
                </FieldGroup>
            </form>

            <div className="w-full h-full p-16">
                <Button variant="secondary" size="lg" className="w-full h-8 drop-shadow-lg drop-shadow-background/20" onClick={() => navigate('/login')}>Go back to login</Button>
            </div>
        </div>
    )
}