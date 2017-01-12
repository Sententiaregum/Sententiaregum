/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Translate    from 'react-translate-component';
import React        from 'react';
import InfoBox      from './login/InfoBox';
import Form         from './login/Form';
import Panel        from 'react-bootstrap/lib/Panel';
import InversedGrid from '../app/markup/InversedGrid';

/**
 * Login component.
 *
 * @returns {React.Element} The landing page markup.
 */
export default () => {
  return (
    <div>
      <h1><Translate content="pages.portal.login.headline" /></h1>
      <InversedGrid>
        <Panel bsStyle="success" header={<Translate content="pages.portal.login.panels.login" />}>
          <Form />
        </Panel>
        <Panel bsStyle="info" header={<Translate content="pages.portal.login.panels.info" />}>
          <InfoBox />
        </Panel>
      </InversedGrid>
    </div>
  );
};
