USE [Smc_uat]
GO

/****** Object:  Table [dbo].[current_cwh_inventories]    Script Date: 5/30/2018 2:48:25 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[current_cwh_inventories](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[store_id] [int] NOT NULL,
	[store_type] [varchar](200) NOT NULL,
	[store_name] [varchar](250) NOT NULL,
	[inventory_status_id] [int] NOT NULL,
	[product_id] [int] NOT NULL,
	[product_name] [varchar](250) NOT NULL,
	[product_type] [varchar](50) NOT NULL,
	[product_brand] [varchar](200) NOT NULL,
	[product_category] [varchar](50) NOT NULL,
	[batch_number] [varchar](100) NULL,
	[expire_date] [date] NULL,
	[m_unit] [varchar](10) NULL,
	[qty] [decimal](13, 2) NOT NULL,
	[updated_at] [datetime] NULL,
	[transaction_date] [date] NOT NULL,
	[transaction_type_id] [int] NULL,
 CONSTRAINT [current_cwh_inventories_PRIMARY] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

