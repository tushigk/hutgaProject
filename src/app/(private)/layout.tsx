import "../../styles/globals.css";
import { ReactNode } from "react";
import { Navbar } from "@/components/nav-bar";
import { Header } from "@/components/header";
import React from "react";
interface LayoutProps {
  children: ReactNode;
}

export default function Layout({ children }: LayoutProps) {
  return (
    <div className="w-full">
      <div className="w-[15%]">
        <Navbar />
      </div>
      <div>
        <Header />
      </div>
      <main className="ml-[15%] flex-1 p-6 overflow-auto">{children}</main>
    </div>
  );
}
