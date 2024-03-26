USE [Smc_uat]
GO

/****** Object:  Table [dbo].[store_map]    Script Date: 5/30/2018 2:49:58 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[store_map](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[esales_store_id] [bigint] NOT NULL,
	[dss_store_id] [bigint] NOT NULL,
 CONSTRAINT [PK_store_mapping] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

