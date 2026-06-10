import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom"
import LoginPage from "@/pages/LoginPage"
import ProtectedRoute from "@/lib/middleware"
import { Toaster } from "sonner"
import RegistrationPage from "./pages/RegistrationPage"
import ForgotPasswordPage from "./pages/ForgotPasswordPage"
import ResetPasswordPage from "./pages/ResetPasswordPage"
import DashboardPage from "./pages/DashboardPage"
import MainLayout from "./components/layouts/MainLayout"
import MembersView from "./components/views/MembersView"
import RolesView from "./components/views/RolesView"
import RoleCreationForm from "./components/forms/RoleCreationForm"
import { RoleUpdateForm } from "./components/forms/RoleUpdateForm"
import { MemberUpdateForm } from "./components/forms/MemberUpdateForm"
import { GroupUpdateForm } from "./components/forms/GroupSettingsForm"
import { GroupCreateForm } from "./components/forms/GroupCreateForm"
import AuditlogView from "./components/views/AuditlogView"
import UserSettingsView from "./components/views/UserSettingsView"
import WorkInProgressPage from "./pages/WorkInProgressPage"
import ProjectsView from "./components/views/ProjectsView"
import ProjectSettingsView from "./components/views/ProjectsSettingsView"
import { ProjectCreateForm } from "./components/forms/ProjectCreationForm"

export function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegistrationPage />} />
          <Route path="/forgot-password" element={<ForgotPasswordPage />} />
          <Route path="/reset-password" element={<ResetPasswordPage />} />
          <Route path="/" element={<Navigate to="/login" replace />} />

          <Route element={<ProtectedRoute />}>
            <Route element={<MainLayout />}>
              <Route path="/dashboard" element={<DashboardPage />} />
              <Route path="/group/members" element={<MembersView />} />
              <Route path="/group/members/invite" element={<WorkInProgressPage />} />
              <Route path="/group/members/manage/roles" element={<MemberUpdateForm />} />
              <Route path="/group/roles" element={<RolesView />} />
              <Route path="/group/roles/create" element={<RoleCreationForm />} />
              <Route path="/group/roles/update" element={<RoleUpdateForm />} />
              <Route path="/group/settings" element={<GroupUpdateForm />} />
              <Route path="/group/auditlog" element={<AuditlogView />} />
              <Route path="/group/projects" element={<ProjectsView />} />
              <Route path="/group/create" element={<GroupCreateForm />} />
              <Route path="/user/settings" element={<UserSettingsView />} />
              <Route path="/user/profile" element={<WorkInProgressPage />} />
              <Route path="/projects/create" element={<ProjectCreateForm />} />
              <Route path="/projects/:projectId" element={<h1>You are viewing a project</h1>} />
              <Route path="/projects/:projectId/settings" element={<ProjectSettingsView />} />
            </Route>
          </Route>
        </Routes>
      </BrowserRouter>

      <Toaster position="top-center" richColors />
    </>
  )
}

export default App
