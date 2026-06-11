import { matchPath, useLocation } from "react-router-dom";

export function useIsViewingProject() {
    const {pathname} = useLocation()

    const match = matchPath("/projects/:projectId/view/*", pathname)

    return !!match
}