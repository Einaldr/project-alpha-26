import { useEffect, useState } from "react";

export const VIEW_TYPES = {
    MOBILE: "MOBILE",
    TABLET: "TABLET",
    DESKTOP: "DESKTOP",
}

export function useViewType() {
    const [viewType, setViewType] = useState(() => {
        if (typeof window == "undefined") return VIEW_TYPES.DESKTOP;
        if (window.innerWidth < 768) return VIEW_TYPES.MOBILE;
        if (window.innerWidth < 1024) return VIEW_TYPES.TABLET;
        return VIEW_TYPES.DESKTOP;
    });

    useEffect(() => {
        const mobileQuery = window.matchMedia("(max-width: 767px)");
        const tabletQuery = window.matchMedia("(min-width: 768px) and (max-width: 1023px)");
        const desktopQuery = window.matchMedia("(min-width: 1024px)");

        const updateViewType = () => {
            if (mobileQuery.matches) setViewType(VIEW_TYPES.MOBILE);
            else if (tabletQuery.matches) setViewType(VIEW_TYPES.TABLET);
            else if (desktopQuery.matches) setViewType(VIEW_TYPES.DESKTOP);
        };

        mobileQuery.addEventListener("change", updateViewType);
        tabletQuery.addEventListener("change", updateViewType);
        desktopQuery.addEventListener("change", updateViewType);

        return () => {
            mobileQuery.removeEventListener("change", updateViewType);
            tabletQuery.removeEventListener("change", updateViewType);
            desktopQuery.removeEventListener("change", updateViewType);
        };
    }, []);

    return viewType;
}