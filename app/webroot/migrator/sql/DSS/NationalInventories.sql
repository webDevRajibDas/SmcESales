USE [SMCDSS]
GO

/****** Object:  Table [dbo].[NationalInventories]    Script Date: 5/30/2018 2:48:25 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[NationalInventories](
	[Id] [bigint] IDENTITY(1,1) NOT NULL,
	[EsalesStoreId] [int] NOT NULL,
	[EsalesStoreName] [varchar](250) NOT NULL,
	[StoreId] [int] NULL,
	[StoreType] [varchar](200) NOT NULL,
	[ProductName] [varchar](250) NOT NULL,
	[ProductType] [varchar](50) NOT NULL,
	[ProductCategory] [varchar](50) NOT NULL,
	[ProductBrand] [varchar](200) NOT NULL,
	[ProductId] [int] NOT NULL,
	[UnitId] [varchar](10) NULL,
	[Qty] [decimal](13, 2) NOT NULL,
	[UpdatedAt] [datetime] NULL,
	[TransactionDate] [date] NULL,
 CONSTRAINT [NationalInventories_PRIMARY] PRIMARY KEY CLUSTERED
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

