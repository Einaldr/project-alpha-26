import { SignOutIcon } from "@phosphor-icons/react"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogMedia,
  AlertDialogTitle,
} from "../ui/alert-dialog"
import { userService } from "@/services/userService"
import { useNavigate } from "react-router-dom"
import { toast } from "sonner"

interface LogoutAlertProps {
  open: boolean
  onOpenChange: (open: boolean) => void
}

export default function LogoutAlert({ open, onOpenChange }: LogoutAlertProps) {
  const navigate = useNavigate()

  async function handleLogout() {
    const logout = () => {
      const data = userService.logout()
      return data
    }

    toast.promise(logout, {
      loading: "Logging out...",
      success: () => {
        localStorage.removeItem('token');
        navigate('/login')
        return "Successfully logged out!"
      },
      error: (err) => {
        return err?.response?.data?.message || 'Something went wrong';
      }
    })
  }

  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogHeader>
            <AlertDialogMedia>
              <SignOutIcon />
            </AlertDialogMedia>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
          </AlertDialogHeader>
          <AlertDialogDescription>
            Are you sure you want to log out?
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Stay</AlertDialogCancel>
          <AlertDialogAction onClick={handleLogout}>Logout</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}
