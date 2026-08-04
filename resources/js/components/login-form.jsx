import { useForm, usePage } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

export function LoginForm({ className, ...props }) {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('login.process'), {
            onSuccess: () => setData('password', ''),
        });
    }

    return (
        <div className={cn('flex flex-col gap-6', className)} {...props}>
            <Card>
                <CardHeader>
                    <CardTitle>Área restrita</CardTitle>
                    <CardDescription>Entre com seu e-mail e senha para acessar o SCA.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={handleSubmit}>
                        <FieldGroup>
                            {flash?.error && (
                                <Alert variant="destructive">
                                    <AlertDescription>{flash.error}</AlertDescription>
                                </Alert>
                            )}
                            <Field data-invalid={!!errors.email}>
                                <FieldLabel htmlFor="email">E-mail</FieldLabel>
                                <Input
                                    id="email"
                                    type="email"
                                    placeholder="voce@exemplo.com"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    aria-invalid={!!errors.email}
                                    required
                                />
                                {errors.email && <FieldError>{errors.email}</FieldError>}
                            </Field>
                            <Field data-invalid={!!errors.password}>
                                <FieldLabel htmlFor="password">Senha</FieldLabel>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    aria-invalid={!!errors.password}
                                    required
                                />
                                {errors.password && <FieldError>{errors.password}</FieldError>}
                            </Field>
                            <Field>
                                <Button type="submit" disabled={processing}>
                                    Entrar
                                </Button>
                                <FieldDescription className="text-center">
                                    Não tem uma conta? <a href={route('register')}>Cadastre-se</a>
                                </FieldDescription>
                            </Field>
                        </FieldGroup>
                    </form>
                </CardContent>
            </Card>
        </div>
    );
}
