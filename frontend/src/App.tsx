import { BrowserRouter, Routes, Route, Navigate} from "react-router-dom"
import { LoginPage } from "./pages/LoginPage"
import ProtectedRoute from "./lib/middlewere"

export function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />

        <Route element={<ProtectedRoute />}>
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/dashboard" element={<h1>Dashboard here</h1>} />
        </Route>
      </Routes>
    </BrowserRouter>
  )
}

export default App
