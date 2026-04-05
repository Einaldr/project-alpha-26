import api from "@/lib/api";
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema";
import { useForm } from "react-hook-form";
import { useNavigate, useSearchParams } from "react-router-dom";
import { toast } from "sonner";
import z from "zod";
import { Field, FieldError, FieldLabel, FieldGroup } from "../ui/field";
import { Input } from "../ui/input";
import { Controller } from "react-hook-form";
import { Button } from "../ui/button";

const ResetPasswordSchema = z.object({
    password: z.string().min(8, 'Password must be at least 8 characters.'),
    confirmPassword: z.string(),
})
.refine((data) => data.password === data.confirmPassword, {message: "Passwords don't match", path: ['confirmPassword']});

export default function ResetPasswordForm() {
    const navigate = useNavigate();

    const [searchParams] = useSearchParams();

    const emailToken = searchParams.get('token');
    const email = searchParams.get('email');

    const form = useForm<z.infer<typeof ResetPasswordSchema>>({
        resolver: standardSchemaResolver(ResetPasswordSchema),
        defaultValues: {
            password: "",
            confirmPassword: "",
        }
    });

    function onSubmit(data: z.infer<typeof ResetPasswordSchema>) {
        const resetPasswordWithDelay = async () => {
            const apiCall = api.post('/auth/password/reset-password', {
                token: emailToken,
                email: email,
                password: data.password,
                password_confirmation: data.confirmPassword,
            });

            const Timer = new Promise((resolve) => setTimeout(resolve, 1000));

            const [response] = await Promise.all([apiCall, Timer]);

            return response.data;
        }

        toast.promise(resetPasswordWithDelay, {
            loading: 'Resetting password...',
            success: () => {
                setTimeout(()=> navigate('/login'), 500);
                return 'Password changed successfully! You should be able to login now';
            },
            error: (err) => {
                return err?.response?.message || 'Something went wrong';
            }
        })
    }

    return (
        <div className='relative flex flex-col w-full h-full items-center'>
            <h1 className="text-4xl text-foreground m-16 font-extrabold">Reset Password</h1>

            <form className="flex flex-col w-full items-center border border-border rounded-sm" onSubmit={form.handleSubmit(onSubmit)}>
                <FieldGroup className='p-4 pr-8 pl-8 rounded-sm mb-2'>
                    <Controller name='password' control={form.control} render={({field, fieldState}) => (
                        <Field>
                            <FieldLabel htmlFor="newPassword">New Password</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='password' id="newPassword" placeholder="Enter new password here" autoComplete="new-password" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />

                    <Controller name='confirmPassword' control={form.control} render={({field, fieldState}) => (
                        <Field>
                            <FieldLabel htmlFor="confirmPassword">Confirm Password</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='password' id="confirmPassword" placeholder="" autoComplete="new-password" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />
                </FieldGroup>

                <Field className="p-8">
                    <Button variant="default" type="submit" size="lg">Reset Password</Button>
                </Field>
            </form>
        </div>
    )
}