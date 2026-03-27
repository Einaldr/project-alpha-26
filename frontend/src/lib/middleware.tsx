import { useState, useEffect } from "react";
import { Outlet, useLocation, useNavigate } from "react-router-dom";
import api from "./api.ts";
import { AuthAlert, type AuthVariant } from "@/components/alerts/AuthAlert"
import axios from "axios";

const ProtectedRoute = () => {
    const [variant, setVariant] = useState<AuthVariant>("unauthenticated");
    const [isValidating, setIsValidating] = useState(true);
    const [showDialog, setShowDialog] = useState(false);
    const location = useLocation();
    const navigate = useNavigate();

    useEffect(() => {
        const verifySession = async () => {
            const token = localStorage.getItem('token');

            if (!token) {
                setVariant("unauthenticated")
                setShowDialog(true);
                setIsValidating(false);
                return;
            }

            let retries = 5;

            while (retries > 0) {
                try {
                    const response = await api.get('/users/me');

                    const status = response?.status ?? 500;

                    if (status === 200) {
                        setIsValidating(false);
                        return;
                    }
                } catch (err) {
                    console.error('Session verification failed', err);

                    if (axios.isAxiosError(err)) {
                        if (err.response?.status === 401) {
                            localStorage.removeItem('token');
                            setVariant("expired");
                            break;
                        } else if (err.response?.status === 403) {
                            setVariant("forbidden");
                            break;
                        } else {
                            setVariant('serverFailure');
                        }

                        retries--;

                        if (retries > 0) {
                            console.warn('Server error. Retrying... (' + retries + ' attempts left)');
                            await new Promise(resolve => setTimeout(resolve, 300));
                        }
                    } else {
                        setVariant('serverFailure');
                    }

                }

            }

            setShowDialog(true);
            setIsValidating(false);

        }

        verifySession();
    }, []);

    useEffect(() => {
        const handleSessionError = (event: Event) => {
            const customEvent = event as CustomEvent;
            setVariant(customEvent.detail);
            setShowDialog(true);
        }

        window.addEventListener('session-error', handleSessionError);

        return () => {
            window.removeEventListener('session-error', handleSessionError);
        };
    }, []);

    if (isValidating) {
        return null;
    }

    if (showDialog) {
        return (
            <AuthAlert isOpen={true} variant={variant} onLogin={() => navigate('/login', { state: { from: location } })} />
        )
    }

    return <Outlet />
};

export default ProtectedRoute;