"use client";
import {
  Table,
  Card,
  Button,
  Spin,
  Tag,
  Alert,
  Modal,
  Divider,
  Input,
  Form,
  Select,
} from "antd";
import {
  PlusOutlined,
  DeleteOutlined,
  EyeOutlined,
  EditOutlined,
} from "@ant-design/icons";
import type { ColumnsType } from "antd/es/table";
import { useState } from "react";
import { poppins } from "../font/font";

interface Item {
  _id: string;
  number: number;
  firstname: string;
  lastname: string;
  phone: string;
  position: string;
  image: string;
  description: string;
}

const EmployeeTable = () => {
  const [selectedBanner, setSelectedBanner] = useState<Item | null>(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [isAddModalVisible, setIsAddModalVisible] = useState(false);
  const [isEditModalVisible, setIsEditModalVisible] = useState(false);
  const [editItem, setEditItem] = useState<Item | null>(null);

  const data: Item[] = [
    {
      _id: "1",
      number: 1,
      firstname: "Лутбат",
      lastname: "Түшиг",
      phone: "99115511",
      image: "",
      position: "Оёдолчин",
      description: "Дэлгэрэнгүй мэдээлэл",
    },
  ];

  const columns: ColumnsType<Item> = [
    {
      title: "№",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Зураг",
      dataIndex: "image",
      key: "image",
    },
    {
      title: "Овог",
      dataIndex: "firstname",
      key: "firstname",
    },
    {
      title: "Нэр",
      dataIndex: "lastname",
      key: "lastname",
    },
    {
      title: "Албан тушаал",
      dataIndex: "position",
      key: "position",
    },
    {
      title: "Утас",
      dataIndex: "phone",
      key: "phone",
    },
    {
      title: "Үйлдэл",
      key: "action",
      render: (_, record: Item) => (
        <div className="flex justify-around">
          <Button
            type="link"
            icon={<EyeOutlined />}
            className="hover:text-blue-600"
            onClick={() => handleViewClick(record)}
          />
          <Button
            type="link"
            icon={<EditOutlined />}
            className="hover:text-blue-600"
            onClick={() => handleEditClick(record)}
          />

          <Button
            type="link"
            danger
            icon={<DeleteOutlined />}
            className="hover:text-red-600"
          />
        </div>
      ),
      width: 160,
      align: "center",
    },
  ];

  const handleViewClick = (record: Item) => {
    setSelectedBanner(record);
    setIsModalVisible(true);
  };

  const handleModalClose = () => {
    setIsModalVisible(false);
    setSelectedBanner(null);
  };

  const handleAddClick = () => {
    setIsAddModalVisible(true);
  };

  const handleAddModalClose = () => {
    setIsAddModalVisible(false);
  };

  const handleAddSubmit = (values: any) => {
    console.log("New Employee:", values);
    setIsAddModalVisible(false);
  };

  const renderFilterForm = () => {
    return (
      <Form layout="inline" className="mb-4">
        <Form.Item>
          <Input placeholder="Нэрээр  шүүх" />
        </Form.Item>
        <Form.Item>
          <Input placeholder="Утасны дугаараар шүүх" />
        </Form.Item>
        <Form.Item>
          <Select placeholder="Албан тушаалаар шүүх" />
        </Form.Item>
      </Form>
    );
  };

  const handleEditClick = (record: Item) => {
    setEditItem(record);
    setIsEditModalVisible(true);
  };

  const handleEditModalClose = () => {
    setIsEditModalVisible(false);
    setEditItem(null);
  };

  const handleEditSubmit = (values: any) => {
    console.log("Edited Employee:", values);
    setIsEditModalVisible(false);
  };

  return (
    <div className="flex flex-col w-full p-4">
      <Card
        bordered={false}
        title="Ажилтан"
        extra={
          <Button
            icon={<PlusOutlined />}
            type="primary"
            onClick={handleAddClick}
          >
            Нэмэх
          </Button>
        }
      >
        {renderFilterForm()}
        <Alert
          message={<p className="mb-0">Нийт тоо: {data.length}</p>}
          type="info"
          className="mb-4"
        />
        <Spin spinning={false}>
          <Table
            dataSource={data}
            columns={columns}
            rowKey={(record) => record._id}
            pagination={false}
          />
        </Spin>
      </Card>

      {selectedBanner && (
        <Modal
          title="Дэлгэрэнгүй мэдээлэл"
          visible={isModalVisible}
          onCancel={handleModalClose}
          footer={null}
          className="p-4 max-w-lg"
        >
          <Divider />
          <div className={`${poppins.className} modal-content space-y-4`}>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Он сар:</span>{" "}
              {selectedBanner.firstname}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Харилцагч:</span>{" "}
              {selectedBanner.lastname}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Албан тушаал:</span>{" "}
              {selectedBanner.position}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Захиалга:</span>{" "}
              {selectedBanner.phone}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">
                Дэлгэрэнгүй мэдээлэл:
              </span>{" "}
              {selectedBanner.description}
            </p>
          </div>
        </Modal>
      )}

      <Modal
        title="Ажилтан нэмэх"
        visible={isAddModalVisible}
        onCancel={handleAddModalClose}
        footer={null}
      >
        <Divider />
        <Form layout="vertical" onFinish={handleAddSubmit}>
          <Form.Item
            label="Овог"
            name="firstname"
            rules={[{ required: false, message: "Овог оруулна уу!" }]}
          >
            <Input placeholder="Овог" />
          </Form.Item>
          <Form.Item
            label="Нэр"
            name="lastname"
            rules={[{ required: false, message: "Нэр оруулна уу!" }]}
          >
            <Input placeholder="Нэр" />
          </Form.Item>
          <Form.Item
            label="Утас"
            name="phone"
            rules={[{ required: false, message: "Утасны дугаар оруулна уу!" }]}
          >
            <Input placeholder="Утасны дугаар" />
          </Form.Item>
          <Form.Item
            label="Албан тушаал"
            name="position"
            rules={[{ required: false, message: "Албан тушаал оруулна уу!" }]}
          >
            <Select placeholder="Албан тушаал" />
          </Form.Item>
          <Form.Item
            label="Дэлгэрэнгүй мэдээлэл"
            name="description"
            rules={[
              { required: false, message: "Дэлгэрэнгүй мэдээлэл оруулна уу!" },
            ]}
          >
            <Input placeholder="Дэлгэрэнгүй мэдээлэл" />
          </Form.Item>
          <Form.Item
            label="Нууц үг"
            name="password"
            rules={[{ required: false, message: "Нууц үг оруулна уу!" }]}
          >
            <Input placeholder="Нууц үг " />
          </Form.Item>
          <Form.Item className="text-center">
            <Button type="primary" htmlType="submit" className="h-9">
              Хадгалах
            </Button>
          </Form.Item>
        </Form>
      </Modal>

      <Modal
        title="Ажилтан засах"
        visible={isEditModalVisible}
        onCancel={handleEditModalClose}
        footer={null}
      >
        <Divider />
        {editItem && (
          <Form
            layout="vertical"
            onFinish={handleEditSubmit}
            initialValues={editItem}
          >
            <Form.Item
              label="Овог"
              name="firstname"
              rules={[{ required: true, message: "Овог оруулна уу!" }]}
            >
              <Input placeholder="Овог" />
            </Form.Item>
            <Form.Item
              label="Нэр"
              name="lastname"
              rules={[{ required: true, message: "Нэр оруулна уу!" }]}
            >
              <Input placeholder="Нэр" />
            </Form.Item>
            <Form.Item
              label="Утас"
              name="phone"
              rules={[{ required: true, message: "Утасны дугаар оруулна уу!" }]}
            >
              <Input placeholder="Утасны дугаар" />
            </Form.Item>
            <Form.Item
              label="Албан тушаал"
              name="position"
              rules={[{ required: true, message: "Албан тушаал оруулна уу!" }]}
            >
              <Select placeholder="Албан тушаал" />
            </Form.Item>
            <Form.Item
              label="Дэлгэрэнгүй мэдээлэл"
              name="description"
              rules={[
                { required: true, message: "Дэлгэрэнгүй мэдээлэл оруулна уу!" },
              ]}
            >
              <Input placeholder="Дэлгэрэнгүй мэдээлэл" />
            </Form.Item>
            <Form.Item className="text-center">
              <Button type="primary" htmlType="submit" className="h-9">
                Хадгалах
              </Button>
            </Form.Item>
          </Form>
        )}
      </Modal>
    </div>
  );
};

export default EmployeeTable;
