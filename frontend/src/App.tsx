import { BrowserRouter, Routes, Route} from "react-router-dom"
import { LoginPage } from "./pages/LoginPage"

export function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        <Route path="/dashboard" element={<h1>Dashboard here</h1>} />
      </Routes>
    </BrowserRouter>
  )
}

export default App
