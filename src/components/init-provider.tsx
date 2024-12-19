"use client";

import { usePathname, useRouter } from "next/navigation";
import React, { createContext, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import useSWR from "swr";
import { logout } from "@/store/auth-slice";
import { authApi } from "@/apis";
import { RootState } from "@/store";
import Loader from "./loader";

interface Props {
  children: React.ReactNode;
}

export const InitContext = createContext({});

export default function InitProvider({ children }: Props) {
  const dispatch = useDispatch();
  const [isClient, setIsClient] = useState(false);
  const { push } = useRouter();
  const pathname = usePathname();
  const { token, user } = useSelector((state: RootState) => state.auth);
  const { data, error } = useSWR(
    token ? `/api/init/${token}` : null,
    async () => {
      const resMe = await authApi.me();
      return {
        resMe,
      };
    },
    {
      revalidateOnFocus: false,
      onError: (err: any) => {
        if (err.statusCode === 401) {
          dispatch(logout());
          push("/login");
        }
        return err;
      },
    }
  );

  // define State
  const state = {};

  // define Function
  const func = {};

  // define Context
  const context = { state, func, user: data };

  useEffect(() => {
    setIsClient(true);
  }, []);

  useEffect(() => {
    if (!token) {
      if (pathname !== "/login") {
        push("/login");
      }
    }
  }, [token, pathname, push]);

  if (error) {
    return <div>Something went wrong</div>;
  }

  if (!isClient || !data) {
    return (
      <div className="w-full h-full flex flex-col justify-center items-center">
        <Loader />
      </div>
    );
  }

  return (
    <InitContext.Provider value={context}>{children}</InitContext.Provider>
  );
}
