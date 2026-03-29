import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom"
import LoginPage from "@/pages/LoginPage"
import ProtectedRoute from "@/lib/middleware"
import { Toaster } from "sonner"
import RegistrationPage from "./pages/RegistrationPage"

export function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegistrationPage />} />
          <Route path="/forgot-password" element={<h1>Forgot password page WIP</h1>} />
          <Route path="/reset-password" element={<h1>Enter new password page WIP</h1>} />

          <Route element={<ProtectedRoute />}>
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route path="/dashboard" element={<h1>Dashboard here</h1>} />
          </Route>
        </Routes>
      </BrowserRouter>

      <Toaster position="top-center" richColors />
    </>
  )
}

export default App
