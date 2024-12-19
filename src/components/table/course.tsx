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

interface Item {
  _id: string;
  number: number;
  name: string;
  type: string;
  price: number;
}

const CourseTable = () => {
  const [selectedBanner, setSelectedBanner] = useState<Item | null>(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [isAddModalVisible, setIsAddModalVisible] = useState(false);
  const [isEditModalVisible, setIsEditModalVisible] = useState(false);
  const [editItem, setEditItem] = useState<Item | null>(null);

  const data: Item[] = [
    {
      _id: "1",
      number: 1,
      name: "2ш оруулга сүлжмэл ризентэй гоёл шидээс/эрсү",
      type: "Өмд/Өвөл зуны",
      price: 10000,
    },
  ];

  const columns: ColumnsType<Item> = [
    {
      title: "№",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Дамжлагын нэр",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Дамжлагын төрөл",
      dataIndex: "type",
      key: "type",
    },
    {
      title: "Үнэ",
      dataIndex: "price",
      key: "price",
    },
    {
      title: "Үйлдэл",
      key: "action",
      render: (_, record: Item) => (
        <div className="flex justify-around">
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
        <Form.Item label="Дамжлагын төрөл" className="w-1/5">
          <Select placeholder="Дамжлагын төрөлөөр шүүх" />
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
        title="Дамжлагын бүртгэл"
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

      <Modal
        title="Дамжлага нэмэх"
        visible={isAddModalVisible}
        onCancel={handleAddModalClose}
        footer={null}
      >
        <Divider />
        <Form layout="vertical" onFinish={handleAddSubmit}>
          <Form.Item
            label="Дамжлагын нэр"
            name="name"
            rules={[{ required: false, message: "Дамжлагын нэр оруулна уу!" }]}
          >
            <Input placeholder="Дамжлагын нэр" />
          </Form.Item>
          <Form.Item
            label="Дамжлагын төрөл"
            name="type"
            rules={[
              { required: false, message: "Дамжлагын төрөл оруулна уу!" },
            ]}
          >
            <Input placeholder="Дамжлагын төрөл" />
          </Form.Item>
          <Form.Item
            label="Дамжлагын үнэлгээ"
            name="price"
            rules={[{ required: false, message: "Үнэ оруулна уу!" }]}
          >
            <Input placeholder="Үнэлгээний мөнгөн дүнгээ оруулна уу! " />
          </Form.Item>

          <Form.Item className="text-center">
            <Button type="primary" htmlType="submit" className="h-9">
              Хадгалах
            </Button>
          </Form.Item>
        </Form>
      </Modal>

      <Modal
        title="Дамжлага засах"
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
              label="Дамжлагын нэр"
              name="name"
              rules={[{ required: true, message: "Дамжлагын нэр оруулна уу!" }]}
            >
              <Input placeholder="Дамжлагын нэр" />
            </Form.Item>
            <Form.Item
              label="Дамжлагын төрөл"
              name="type"
              rules={[
                { required: true, message: "Дамжлагын төрөл оруулна уу!" },
              ]}
            >
              <Input placeholder="Дамжлагын төрөл" />
            </Form.Item>
            <Form.Item
              label="Үнэ"
              name="price"
              rules={[{ required: true, message: "Үнэ оруулна уу!" }]}
            >
              <Input placeholder="Үнэ" />
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

export default CourseTable;
