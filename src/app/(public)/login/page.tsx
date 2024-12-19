"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";
import { Form, Input, Button, Checkbox } from "antd";
import { Poppins } from "next/font/google";
import React from "react";
const poppins = Poppins({
  weight: "400",
  subsets: ["latin"],
});

export default function Login() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [form] = Form.useForm();
  return (
    <Form
      form={form}
      layout="vertical"
      initialValues={{
        phone: "",
        password: "",
      }}
    >
      <Form.Item
        label={<p className={poppins.className}>Phone number</p>}
        name="phone"
        rules={[{ required: false, message: "Утасны дугаар оруулна уу" }]}
      >
        <Input
          className="text-lg py-3 px-4 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500"
          placeholder="Утасны дугаар"
        />
      </Form.Item>
      <Form.Item
        label={<span className={poppins.className}>Password</span>}
        name="password"
        rules={[{ required: false, message: "Нууц үг оруулна уу" }]}
      >
        <Input.Password
          className="text-lg py-3 px-4 border border-gray-300 rounded-lg w-full focus:ring-2 focus:ring-blue-500"
          placeholder="Нууц үг"
        />
      </Form.Item>
      <div className="flex justify-between items-center">
        <Checkbox className={poppins.className}>Remember password</Checkbox>
        <div className={`${poppins.className} text-sm text-blue-600`}>
          Forgot password?
        </div>
      </div>
      <Form.Item className="mt-6">
        <Button
          type="primary"
          htmlType="submit"
          href="/home"
          loading={loading}
          className={`${poppins.className} py-3 w-full h-[40px] bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transform transition duration-300 ease-in-out tracking-wide`}
        >
          Нэвтрэх
        </Button>
      </Form.Item>
    </Form>
  );
}
