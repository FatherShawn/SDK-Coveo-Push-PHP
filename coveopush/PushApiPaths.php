<?php
// -------------------------------------------------------------------------------------
// CoveoPermissions
// -------------------------------------------------------------------------------------
// Contains the Permissions which are used inside the CoveoDocument
//   PermissionSets, PermisionLevels and Permissions
// -------------------------------------------------------------------------------------
namespace Coveo\Search\SDK\SDKPushPHP;

class PushApiPaths{
    const SOURCE_ACTIVITY_STATUS = "{endpoint}/organizations/{org_id}/sources/{src_id}/status";
    const SOURCE_DOCUMENTS = "{endpoint}/organizations/{org_id}/sources/{src_id}/documents";
    const SOURCE_DOCUMENTS_BATCH = "{endpoint}/organizations/{org_id}/sources/{src_id}/documents/batch";
    const SOURCE_DOCUMENTS_DELETE = "{endpoint}/organizations/{org_id}/sources/{src_id}/documents/olderthan";
    const DOCUMENT_GET_CONTAINER = "{endpoint}/organizations/{org_id}/files";
    const PROVIDER_PERMISSIONS = "{endpoint}/organizations/{org_id}/providers/{prov_id}/permissions";
    const PROVIDER_PERMISSIONS_DELETE = "{endpoint}/organizations/{org_id}/providers/{prov_id}/permissions/olderthan";
    const PROVIDER_PERMISSIONS_BATCH = "{endpoint}/organizations/{org_id}/providers/{prov_id}/permissions/batch";
    const PROVIDER_MAPPINGS = "{endpoint}/organizations/{org_id}/providers/{prov_id}/mappings";
}
