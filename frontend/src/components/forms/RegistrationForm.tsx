import { Controller, useForm } from "react-hook-form";
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Field, FieldContent, FieldError, FieldGroup, FieldLabel, FieldTitle } from "../ui/field";
import z from "zod";
import { Input } from "../ui/input";
import { Checkbox } from "../ui/checkbox";
import { Button } from "../ui/button";
import { Link, useNavigate } from "react-router-dom";
import api from "@/lib/api";
import { toast } from "sonner";

const RegistrationFormSchema = z.object({
    username: z.string(),
    email: z.email(),
    password: z.string(),
    confirmPassword: z.string(),
    termsAndPrivacy: z.boolean().default(false).refine((val) => val === true, { message: "You must accept the terms and conditions to proceed." }),
})

const RegistrationForm = () => {
    const navigate = useNavigate();

    const form = useForm<z.infer<typeof RegistrationFormSchema>>({
        resolver: standardSchemaResolver(RegistrationFormSchema),
        defaultValues: {
            termsAndPrivacy: false,
        }
    });

    function onSubmit(data: z.infer<typeof RegistrationFormSchema>) {
        const registerWithDelay = async () => {
            const apiCall = api.post('/auth/register', {
                name: data.username,
                email: data.email,
                password: data.password,
                password_confirmation: data.confirmPassword,
                tos_accepted: data.termsAndPrivacy,
                privacy_policy_acknowledged: data.termsAndPrivacy
            });

            const timer = new Promise((resolve) => setTimeout(resolve, 1000));

            const [response] = await Promise.all([apiCall, timer]);

            return response.data;
        }

        toast.promise(registerWithDelay, {
            loading: 'Registrating...',
            success: (data) => {
                localStorage.setItem('token', data.token);

                setTimeout(() => navigate('/dashboard'), 500);

                return 'Welcome, ' + data.user.name + '!';
            },
            error: (err) => {
                return err?.response?.message || 'Something went wrong';
            }
        })
    }

    return (
        <div className='relative flex flex-col w-full h-full items-center'>
            <h1 className="text-4xl text-foreground m-16 font-extrabold">Register</h1>

            <form className="flex flex-col w-full items-center" onSubmit={form.handleSubmit(onSubmit)}>
                <FieldGroup className='p-4 pr-8 pl-8 border border-border rounded-sm mb-2'>
                    <Controller name="username" control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldLabel htmlFor="registration-information-username">Username</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='text' id='registration-information-username' placeholder='ExampleUser123' autoComplete="off" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />

                    <Controller name="email" control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldLabel htmlFor="registration-information-email">Email</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='text' id='registration-information-email' placeholder='example@email.com' autoComplete="email" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />
                </FieldGroup>
                <FieldGroup className='p-4 pr-8 pl-8 border border-border rounded-sm mb-2'>
                    <Controller name="password" control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldLabel htmlFor="registration-passwords-password">Password</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='password' id='registration-passwords-password' placeholder='' autoComplete="new-password" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />
                    <Controller name="confirmPassword" control={form.control} render={({ field, fieldState }) => (
                        <Field data-invalid={fieldState.invalid}>
                            <FieldLabel htmlFor="registration-passwords-confirm">Confirm Password</FieldLabel>
                            <Input{...field} aria-invalid={fieldState.invalid} type='password' id='registration-passwords-confirm' placeholder='' autoComplete="new-password" required />
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />
                </FieldGroup>
                <FieldGroup className='flex flex-row p-4 pr-8 pl-8 border border-border rounded-sm'>
                    <Controller name="termsAndPrivacy" control={form.control} render={({ field, fieldState }) => (
                        <Field orientation="horizontal" data-invalid={fieldState.invalid}>
                            <Checkbox id="registration-checkboxes-termsAndPrivacy" checked={field.value} onCheckedChange={field.onChange} />
                            <FieldContent>
                                <FieldTitle>Accept <Link to="/terms-and-conditions" className="hover:text-primary underline">Terms & Conditions</Link> and acknowledge <Link to="/privacy-policy" className="hover:text-primary underline">Privacy Policy</Link></FieldTitle>
                            </FieldContent>
                            {fieldState.invalid && (
                                <FieldError errors={[fieldState.error]} />
                            )}
                        </Field>
                    )} />
                </FieldGroup>

                <Field className="p-16">
                    <Button type="submit" variant="default" size="lg">Register</Button>
                </Field>
            </form>

            <div className="w-full h-full p-16">
                <Button variant="secondary" size="lg" className="w-full h-8 drop-shadow-lg drop-shadow-background/20" onClick={() => navigate('/login')}>Go back to login</Button>
            </div>
        </div>
    )
}

export default RegistrationForm;