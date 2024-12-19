"use client";
import React, { useState } from "react";
import {
  Table,
  Card,
  Button,
  Spin,
  Alert,
  Modal,
  Divider,
  Form,
  Input,
  Upload,
  Select,
} from "antd";
import {
  PlusOutlined,
  EyeOutlined,
  EditOutlined,
  DeleteOutlined,
  ArrowUpOutlined,
  HistoryOutlined,
  UploadOutlined,
} from "@ant-design/icons";
import type { ColumnsType } from "antd/es/table";

interface Item {
  _id: string;
  number: number;
  productName: string;
  price: number;
  remaining: number;
  amount: number;
}
interface Deposit {
  id: string;
  date: string;
  name: string;
  quantity: number;
  price: number;
  amount: number;
  description: string;
}
interface Withdraw {
  id: string;
  date: string;
  name: string;
  transactionValue: string;
  type: string;
  quantity: number;
  price: number;
  amount: number;
}

const ProductTable = () => {
  const [selectedProduct, setSelectedProduct] = useState<Item | null>(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [isAddModalVisible, setIsAddModalVisible] = useState(false);
  const [isEditModalVisible, setIsEditModalVisible] = useState(false);
  const [isDepositModalVisible, setIsDepositModalVisible] = useState(false);

  const data: Item[] = [
    {
      _id: "1",
      number: 1,
      productName: "Хот тохижилт өвлийн хос",
      price: 19800,
      remaining: 32,
      amount: 633600,
    },
  ];

  const depositData = [
    {
      id: "1",
      date: "2024-12-01",
      name: "Хот тохижилт өвлийн хос",
      quantity: 34,
      price: 198000,
      amount: 6732000,
      description: "Хот тохижилт өвлийн хос",
    },
  ];

  const withdrawData = [
    {
      id: "1",
      date: "2024-12-03",
      name: "Сүндэр",
      transactionValue: "Дэлгүүрийн өвлийн хослол авав",
      type: "Хувь хүн",
      quantity: 1,
      price: 150000,
      amount: 150000,
    },
    {
      id: "2",
      date: "2024-12-04",
      name: "Сүндэр",
      transactionValue: "Дэлгүүрийн өвлийн хослол авав",
      type: "Хувь хүн",
      quantity: 1,
      price: 150000,
      amount: 150000,
    },
  ];

  const columns: ColumnsType<Item> = [
    {
      title: "№",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Барааны нэр",
      dataIndex: "productName",
      key: "productName",
    },
    {
      title: "Барааны үнэ",
      dataIndex: "price",
      key: "price",
    },
    {
      title: "Үлдэгдэл",
      dataIndex: "remaining",
      key: "remaining",
    },
    {
      title: "Нийт үнэ",
      dataIndex: "amount",
      key: "amount",
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
            className="hover:text-blue-600"
          />
        </div>
      ),
      width: 160,
      align: "center",
    },
  ];

  const handleViewClick = (record: Item) => {
    setSelectedProduct(record);
    setIsModalVisible(true);
  };
  const handleEditClick = (record: Item) => {
    setSelectedProduct(record);
    setIsEditModalVisible(true);
  };

  const handleModalClose = () => {
    setIsModalVisible(false);
    setSelectedProduct(null);
  };

  const handleAddClick = () => {
    setIsAddModalVisible(true);
  };
  const handleDeposit = () => {
    setIsDepositModalVisible(true);
  };

  const handleAddModalClose = () => {
    setIsAddModalVisible(false);
  };

  const handleDepositClose = () => {
    setIsDepositModalVisible(false);
  };
  const handleEditModalClose = () => {
    setIsEditModalVisible(false);
    setSelectedProduct(null);
  };

  const handleEditSubmit = (values: any) => {
    console.log("Updated values:", values);
    setIsEditModalVisible(false);
  };

  const depositColumns: ColumnsType<Deposit> = [
    {
      title: "Он сар",
      dataIndex: "date",
      key: "date",
    },
    {
      title: "Харилцагч",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Тоо ширхэг",
      dataIndex: "quantity",
      key: "quantity",
    },
    {
      title: "Нэгж үнэ",
      dataIndex: "price",
      key: "price",
    },
    {
      title: "Нийт үнэ",
      dataIndex: "amount",
      key: "amount",
    },
    {
      title: "Тайлбар",
      dataIndex: "description",
      key: "description",
    },
    {
      title: "Үйлдэл",
      key: "action",
      render: (_, record: Deposit) => (
        <div className="flex justify-around">
          <Button
            type="link"
            icon={<EditOutlined />}
            className="hover:text-blue-600"
          />
          <Button
            type="link"
            danger
            icon={<DeleteOutlined />}
            className="hover:text-blue-600"
          />
        </div>
      ),
    },
  ];
  const withdrawColumns: ColumnsType<Withdraw> = [
    {
      title: "Он сар",
      dataIndex: "date",
      key: "date",
    },
    {
      title: "Харилцагч",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Транзакци",
      dataIndex: "transactionValue",
      key: "transactionValue",
    },
    {
      title: "Төрөл",
      dataIndex: "type",
      key: "type",
    },
    {
      title: "Тоо ширхэг",
      dataIndex: "quantity",
      key: "quantity",
    },
    {
      title: "Нэгж үнэ",
      dataIndex: "price",
      key: "price",
    },
    {
      title: "Нийт үнэ",
      dataIndex: "amount",
      key: "amount",
    },
  ];

  return (
    <div className="flex flex-col w-full p-4">
      <Card
        bordered={false}
        title="Бэлэн барааны мэдээлэл"
        extra={
          <div className="flex space-x-2">
            <Button
              icon={<PlusOutlined />}
              type="primary"
              onClick={handleAddClick}
            >
              Нэмэх
            </Button>
            <Button
              icon={<ArrowUpOutlined />}
              type="default"
              onClick={handleDeposit}
            >
              Зарлага хийх
            </Button>
            <Button icon={<HistoryOutlined />} type="default">
              Зарлагын түүх
            </Button>
          </div>
        }
      >
        {data.length > 0 ? (
          <>
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
          </>
        ) : (
          <Alert
            message="No products available"
            type="warning"
            className="mb-4"
          />
        )}
      </Card>

      {selectedProduct && (
        <Modal
          title="Гүйлгээний мэдээлэл"
          visible={isModalVisible}
          onCancel={handleModalClose}
          width={1300}
          footer={null}
        >
          <Divider />
          <Card title="Орлогын түүх">
            <Table
              dataSource={depositData}
              columns={depositColumns}
              rowKey={(record) => record.id}
              pagination={false}
            />
          </Card>
          <Card title="Зарлагын түүх" className="mt-5">
            <Table
              dataSource={withdrawData}
              columns={withdrawColumns}
              rowKey={(record) => record.id}
              pagination={false}
            />
          </Card>
          <div className="mt-5 justify-center flex space-x-4">
            <Button className="bg-orange-500 text-white">
              Excel - руу хөрвүүлэх
            </Button>
            <Button className="bg-green-500 text-white">Хэвлэх</Button>
          </div>
        </Modal>
      )}

      {selectedProduct && (
        <Modal
          title="Бараа засварлах"
          visible={isEditModalVisible}
          onCancel={handleEditModalClose}
          footer={null}
          className="p-4 max-w-lg"
        >
          <Divider />
          <Form
            layout="vertical"
            initialValues={{
              name: selectedProduct.productName,
              price: selectedProduct.price,
              remaining: selectedProduct.remaining,
            }}
            onFinish={handleEditSubmit}
          >
            <Form.Item
              label="Барааны нэр"
              name="name"
              rules={[{ required: true, message: "Барааны нэрийг оруулна уу" }]}
            >
              <Input />
            </Form.Item>
            <Form.Item
              label="Барааны үнэ"
              name="price"
              rules={[{ required: true, message: "Барааны үнийг оруулна уу" }]}
            >
              <Input type="number" />
            </Form.Item>
            <Form.Item
              label="Үлдэгдэл"
              name="remaining"
              rules={[
                { required: true, message: "Үлдэгдлийн тоог оруулна уу" },
              ]}
            >
              <Input type="number" />
            </Form.Item>
            <Form.Item className="text-center">
              <Button type="primary" htmlType="submit" className="h-9">
                Хадгалах
              </Button>
            </Form.Item>
          </Form>
        </Modal>
      )}

      <Modal
        title="Бэлэн барааны бүртгэл нэмэх"
        visible={isAddModalVisible}
        onCancel={handleAddModalClose}
        className="p-4 max-w-lg"
        footer={null}
      >
        <Divider />
        <Form layout="vertical">
          <Form.Item
            label="Зураг"
            name="image"
            valuePropName="fileList"
            getValueFromEvent={(e) => (Array.isArray(e) ? e : e?.fileList)}
            rules={[{ required: true, message: "Зураг оруулна уу!" }]}
          >
            <Upload
              name="image"
              listType="picture"
              beforeUpload={() => false}
              accept="image/*"
            >
              <Button icon={<UploadOutlined />}>Зураг оруулах</Button>
            </Upload>
          </Form.Item>
          <Form.Item
            label="Барааны нэр"
            name="name"
            rules={[{ required: false, message: "Барааны нэрийг оруулна уу" }]}
          >
            <Input />
          </Form.Item>
          <Form.Item
            label="Барааны үнэ"
            name="price"
            rules={[{ required: false, message: "Барааны үнийг оруулна уу" }]}
          >
            <Input type="number" />
          </Form.Item>

          <Form.Item className="text-center">
            <Button type="primary" htmlType="submit" className="h-9">
              Хадгалах
            </Button>
          </Form.Item>
        </Form>
      </Modal>
      <Modal
        title="Бэлэн барааны зарлага хийх"
        onCancel={handleDepositClose}
        visible={isDepositModalVisible}
        className="p-4 max-w-lg"
        footer={null}
      >
        <Divider />
        <Form layout="vertical">
          <Form.Item
            label="Барааны нэр"
            name="productName"
            rules={[{ required: false, message: "Барааны нэрийг оруулна уу" }]}
          >
            <Select placeholder="Барааны нэр сонгоно уу!" />
          </Form.Item>
          <Form.Item
            label="Худалдан авагчийн нэр"
            name="name"
            rules={[
              { required: false, message: "Худалдан авагчийн нэр  оруулна уу" },
            ]}
          >
            <Input placeholder="Худалдан авагчийн нэр  оруулна уу!" />
          </Form.Item>
          <Form.Item
            label="Гүйлгээний утга"
            name="transactionValue"
            rules={[
              { required: false, message: "Гүйлгээний утга  оруулна уу" },
            ]}
          >
            <Input placeholder="Гүйлгээний утга  оруулна уу!" />
          </Form.Item>
          <Form.Item
            label="Тоо ширхэг"
            name="quantity"
            rules={[{ required: false, message: "Тоо ширхэг  оруулна уу" }]}
          >
            <Input placeholder="Тоо ширхэг оруулна уу!" />
          </Form.Item>
          <Form.Item
            label="Нэгжийн үнэ"
            name="price"
            rules={[{ required: false, message: "Нэгжийн үнийг оруулна уу" }]}
          >
            <Input placeholder="Нэгжийн үнэ оруулна уу!" />
          </Form.Item>
          <Form.Item
            label="Худалдан авагчийн төрөл"
            name="price"
            rules={[
              {
                required: false,
                message: "Худалдан авагчийн төрөл  оруулна уу",
              },
            ]}
          >
            <Input placeholder="Худалдан авагчийн төрөл  оруулна уу!" />
          </Form.Item>

          <Form.Item className="text-center">
            <Button type="primary" htmlType="submit" className="h-9">
              Хадгалах
            </Button>
          </Form.Item>
        </Form>
      </Modal>
    </div>
  );
};

export default ProductTable;
