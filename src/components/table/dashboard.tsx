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
  Form,
  Select,
  DatePicker,
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
  date: string;
  relationship: string;
  order: string;
  channel: string;
  rating: number;
  total: number;
  employee: string;
  responsibleNumber: number;
  totalRating: number;
  Type: string;
}

const DashboardTable = () => {
  const [selectedBanner, setSelectedBanner] = useState<Item | null>(null);
  const [isModalVisible, setIsModalVisible] = useState(false);

  const data: Item[] = [
    {
      _id: "1",
      number: 1,
      date: "2022-01-01",
      relationship: "Борис",
      order: "Захиалга",
      channel: "Дамжлага",
      rating: 4.5,
      total: 10000,
      employee: "Анна",
      responsibleNumber: 5,
      totalRating: 4.5,
      Type: "Дууссан",
    },
  ];

  const columns: ColumnsType<Item> = [
    {
      title: "№",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Он сар",
      dataIndex: "date",
      key: "date",
    },
    {
      title: "Харилцагч",
      dataIndex: "relationship",
      key: "relationship",
      render: (relationship: string) => <Tag color="blue">{relationship}</Tag>,
    },
    {
      title: "Захиалга",
      dataIndex: "order",
      key: "order",
    },
    {
      title: "Дамжлагын нэр",
      dataIndex: "channel",
      key: "channel",
    },
    {
      title: "Үнэлгээ",
      dataIndex: "rating",
      key: "rating",
    },
    {
      title: "Нийт тоо",
      dataIndex: "total",
      key: "total",
    },
    {
      title: "Хариуцсан ажилтан",
      dataIndex: "employee",
      key: "employee",
      render: (employee: string) => <Tag color="blue">{employee}</Tag>,
    },
    {
      title: "Хариуцсан тоо",
      dataIndex: "responsibleNumber",
      key: "responsibleNumber",
    },
    {
      title: "Нийт үнэлгээ",
      dataIndex: "totalRating",
      key: "totalRating",
    },
    {
      title: "Төрөл",
      dataIndex: "Type",
      key: "Type",
      render: (Type: string) => <Tag color="green">{Type}</Tag>,
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

  const renderFilterForm = () => {
    return (
      <Form layout="inline" className="mb-4">
        <Form.Item label="Харилцагч">
          <Select placeholder="Бүгд" allowClear style={{ width: 200 }}>
            <Select.Option value="pending">Хүлээгдэж байна</Select.Option>
            <Select.Option value="approved">Баталгаажсан</Select.Option>
            <Select.Option value="rejected">Цуцлагдсан</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label="Захиалга">
          <Select placeholder="Бүгд" allowClear style={{ width: 200 }}>
            <Select.Option value="pending">Хүлээгдэж байна</Select.Option>
            <Select.Option value="approved">Баталгаажсан</Select.Option>
            <Select.Option value="rejected">Цуцлагдсан</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label="Дамжлага">
          <Select placeholder="Бүгд" allowClear style={{ width: 200 }}>
            <Select.Option value="pending">Хүлээгдэж байна</Select.Option>
            <Select.Option value="approved">Баталгаажсан</Select.Option>
            <Select.Option value="rejected">Цуцлагдсан</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label="Ажилтан">
          <Select placeholder="Бүгд" allowClear style={{ width: 200 }}>
            <Select.Option value="pending">Хүлээгдэж байна</Select.Option>
            <Select.Option value="approved">Баталгаажсан</Select.Option>
            <Select.Option value="rejected">Цуцлагдсан</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label="Эхлэх огноо">
          <DatePicker />
        </Form.Item>
        <Form.Item label="Дуусах огноо">
          <DatePicker />
        </Form.Item>
      </Form>
    );
  };

  return (
    <div className="flex flex-col w-full p-4">
      <Card
        bordered={false}
        title="Нийт дамжлагын мэдээлэл"
        extra={
          <Button icon={<PlusOutlined />} type="primary">
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
              {selectedBanner.date}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Харилцагч:</span>{" "}
              {selectedBanner.relationship}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Захиалга:</span>{" "}
              {selectedBanner.order}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Дамжлагын нэр:</span>{" "}
              {selectedBanner.channel}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Үнэлгээ:</span>{" "}
              {selectedBanner.rating}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Нийт тоо:</span>{" "}
              {selectedBanner.total}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">
                Хариуцсан ажилтан:
              </span>{" "}
              {selectedBanner.employee}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Хариуцсан тоо:</span>{" "}
              {selectedBanner.responsibleNumber}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Нийт үнэлгээ:</span>{" "}
              {selectedBanner.totalRating}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Төрөл:</span>{" "}
              {selectedBanner.Type}
            </p>
          </div>
          <div className="mt-5 justify-center flex space-x-4">
            <Button className="bg-orange-500 text-white">
              Excel - руу хөрвүүлэх
            </Button>
            <Button className="bg-green-500 text-white">Хэвлэх</Button>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default DashboardTable;
